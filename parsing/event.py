import re # Library  for regexp
import mapper
import parsingTools as parse
from collections import Counter
import dictTool
eventFilter = { # and condition
		# 'name':None,
		# 'waits':1,
		# 'timeouts':None,
		# 'totalWaitTime':None,
		#'waitTxn':None,
		# 'totalCallTime':0,
		'dbTime':0,
		# 'bgTime':0,

		# 'avgWait':1
}
eventArrayIndexMap = {
	'default':{
		'name':0,
		'waits':1,
		'timeouts':2,
		'time':3,
		'avgWait':4,
		'waitTxn':5,
		'dbTime':6
	},
	'awr-..-11\.2\.0\..*':{
	
	},
	'awr-..-11\.1\.0\..*':{	

	},
	'sp-..-11\.2\.0\..*':{	
	},
	'.*9\.2\.0\..*':{
	},
	'.*10\.2.*':{				#only AWR checked
		'dbTime':None
	},

	'awr-..-12\.1.*':{
		'time':None,
	},
	'sp-..-12\.1.*':{

	}
}
eventHistogramIndexMap = {
	'default':{
		'name':0,
		'1ms':2,
		'2ms':3,
		'4ms':4,
		'8ms':5,
		'16ms':6,
		'32ms':7,
		'1s':8,
		'1sP':9
	}
}
startPointMap = {
	'default':{
		'foreground':'Foreground Wait Events((?:.|\n)(?!Event   ))*',
		'background':'Background Wait Events((?:.|\n)(?!Event   ))*',
		'both': 'Wait Events \(fg and bg\)((?:.|\n)(?!Event   ))*',
		'delim': 'Wait Events(.|\n)*-{15,}.*\n',
		'histogram':'Wait Event Histogram((?:.|\n)(?!Event   ))*'
	},
	'awr-..-11\.2\.0\..*':{
		# 'both':None
	},
	'sp-..-11\.2\.0\..*':{
		# 'both':None
	},
	'awr-..-11\.1\.0\..*':{
		'both':None
	},
	'.*9\.2\.0\..*':{
		'foreground':None,
		'histogram': None,
		'both':'Wait Events for((?:.|\n)(?!Event   ))*'
	},
	'.*10\.2\..*':{
		'foreground':None,
		'histogram': None,
		'both':'Wait Events   ((?:.|\n)(?!Event   ))*'
	},
	'.*10\.1\..*':{
		'foreground':None,
		'histogram': None,
		'both':	'Wait Events ((?:.|\n)(?!Event   ))*'
	},

	'.*12\.1\.0\.2':{
		'both':None
	}
}

def getEvents(fileString,version):
	"""Get all the event from a statsPack/AWR and return a map containing all the indicator for the events"""
	startPointMapper = mapper.Mapper(startPointMap,version)
	eventArrayIndexMapper = mapper.Mapper(eventArrayIndexMap,version)
	start = startPointMapper.get('both')
	bothEvents = {}
	if(start is not None):					#Case we have the sum done by Oracle
		substring = parse.getSubString(fileString,start)
		if(substring != None):
			substring = re.sub(startPointMapper.get('delim'),'',substring)
			eventArray = parse.getArrayFromDelim(substring)	#we extract an array containing all events
			bothEvents = _getEventsFromArray(eventArrayIndexMapper,eventArray,eventFilter)
	foregroundStart = startPointMapper.get('foreground')
	backgroundStart = startPointMapper.get('background')
	fgAndBgEvents = {}
	if (foregroundStart != None and backgroundStart != None):
		substring = parse.getSubString(fileString,foregroundStart)
		substring = re.sub(startPointMapper.get('delim'),'',substring)
		backgroundEventsArray = parse.getArrayFromDelim(substring)				#we extract an array containing all background events
		backgroundEvents = _getEventsFromArray(eventArrayIndexMapper,backgroundEventsArray,eventFilter)					#we make an associative map with the indicator name as key
		substring = parse.getSubString(fileString,backgroundStart)
		substring = re.sub(startPointMapper.get('delim'),'',substring)
		foregroundEventsArray = parse.getArrayFromDelim(substring)		#we extract an array containing all foreground events
		foregroundEvents = _getEventsFromArray(eventArrayIndexMapper,foregroundEventsArray,eventFilter)					#we make an associative map with the indicator name as key
		keys = list(set(foregroundEvents.keys()) | set(backgroundEvents.keys()))	#merge without duplicate the key of background and foreground
		for key in keys :
			name = key 
			if key in foregroundEvents and key in backgroundEvents: 				#Case we have the event in both the foreground and the background
				row = dictTool.add([foregroundEvents[key],backgroundEvents[key]])	#we sum the row from the foreground and the background row
			elif key in foregroundEvents: 											#Case Only in foreground
				row = foregroundEvents[key]				
			else :																	#Case Only in background
				row = backgroundEvents[key]													
			row['name'] = name														#we save the key (name) as a element of the array 
			fgAndBgEvents[name] = row
	events = dict(fgAndBgEvents, **bothEvents)
	histogramStart = startPointMapper.get('histogram')
	if histogramStart is not None :
		eventsHistogrammArray = parse.getArrayFromDelim(parse.getSubString(fileString,histogramStart))
		eventHistogramIndexMapper = mapper.Mapper(eventHistogramIndexMap,version)
		histogramEvent = _getEventsFromArray(eventHistogramIndexMapper,eventsHistogrammArray,eventFilter)
		for name,event in histogramEvent.iteritems() : 
			if name in fgAndBgEvents :
				events[name]['histo'] = event
	for name in events:
		events[name]['name'] = name
	return events.values()

def _getEventsFromArray(indexMapper,array,filter = None):
	fgAndBgEvents = dict()
	for row in array :
		event = dict()
		index = indexMapper.get('name')
		name = row[index]
		for key,index in indexMapper.iteritems():
			if(index is not None and index <= len(row)):
				value = parse.toFloat(row[index])

				if (value is not None):
					event[key] = value
			if name != '':
				selected = True
				for key,filterValue in filter.iteritems():
					if key in event :
						eventValue = event[key]
					else :
						eventValue = None
					if eventValue is None or eventValue < filterValue :
						selected = False
				if selected :
					fgAndBgEvents[name] = event
	return fgAndBgEvents