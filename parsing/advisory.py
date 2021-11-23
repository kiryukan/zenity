import re # Library  for regexp
import mapper
import parsingTools as parse
import dictTool
import logging
limitedPick = [0.9,1.1,1.2,1.3]
advisoryMap ={
	"default":{
		'shared pool':{
			'start_point':"Shared Pool Advisory ",
			'end_point':"-{40,}",
			'index':{
				'size':0,
				'sizeFctr':1,
				'gainFctr':5,
			}
		},		
		'buffer pool':{
			'start_point':"Buffer Pool Advisory ",
			'end_point':"-{40,}",
			'index':{
				'size':1,
				'sizeFctr':2,
				'gainFctr':4,
			}
		},
		'pga memory':{
			'start_point':"PGA Memory Advisory ",
			'end_point':"-{40,}",
			'index':{
				'size':0,
				'sizeFctr':1,
				'gainFctr':5,
			}
		},
		'sga memory':{
			'start_point':"SGA Target Advisory ",
			'end_point':"-{40,}",
			'index':{
				'size':0,
				'sizeFctr':1,
				'gainFctr':3,
			}
		},
	}
}

def getAdvisory(fileString,version):
	"""Get advisory for memory area from oracle"""
	advisoryMapper = mapper.Mapper(advisoryMap,version)
	advisories = []
	for advisoryName,advisoryDelim in advisoryMapper.iteritems():
		advisory = {}
		advisory['name'] = advisoryName
		advisoryValue = {}
		textArray = parse.getSubString(fileString,advisoryDelim['start_point'],advisoryDelim['end_point'])
		if (textArray):
			array = parse.getArrayFromDelim(textArray)
			oldSizeFctr = None
			repeat = 0
			for row in array[:-1]:
				sizeFctr = parse.toFloat(row[advisoryDelim['index']['sizeFctr']])
				if sizeFctr != oldSizeFctr and oldSizeFctr != None:
					for index,value in advisoryValue[oldSizeFctr].iteritems():
						advisoryValue[oldSizeFctr][index] /= repeat
					repeat = 1
				else :
					repeat +=1
				oldSizeFctr = sizeFctr
				if(sizeFctr not in advisoryValue.keys()):
					advisoryValue[sizeFctr] = {}
				for name,index in advisoryDelim['index'].iteritems():
					if name not in advisoryValue[sizeFctr].keys():
						advisoryValue[sizeFctr][name] = 0
					advisoryValueToAdd = parse.toFloat(row[index])
					if advisoryValueToAdd is not None:
						advisoryValue[sizeFctr][name] += advisoryValueToAdd
		advisory['advisoryMap'] = advisoryValue
		advisories.append(advisory)
	return advisories
def getLimitedAdvisory(fileString,version):
	advisories = getAdvisory(fileString,version)
	limitedAdvisories = []
	for advisory in advisories:
		limitedAdvisory = {}
		limitedAdvisory['name'] = advisory['name']
		limitedAdvisory['advisoryMap'] = {}
		for keyPick in limitedPick :
			if(keyPick in advisory['advisoryMap'].keys()):
				limitedAdvisory['advisoryMap'][keyPick] = advisory['advisoryMap'][keyPick]
		limitedAdvisories.append(limitedAdvisory)
	return limitedAdvisories