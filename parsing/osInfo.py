import re # Library  for regexp
import mapper
import parsingTools as parse
from collections import Counter
import dictTool
osArrayDelimMap={
		'default':{
			'startPoint':'Operating System Statistics',
			'startPointBis': 'Statistic',
			'endPoint':'\n\n'
		},
		'sp-..-11.*':{
			'startPoint':'OS Statistics',
		},
		'sp-..-12\..*':{
			'startPoint':'OS Statistics',
		}
}
osArrayStartMap = {
	'default':{
		'busyTime':'BUSY_TIME',
		'idleTime':'IDLE_TIME',
		'iowaitTime':'IOWAIT_TIME',
		'niceTime':'NICE_TIME',
		'sysTime':'SYS_TIME',
		'userTime':'USER_TIME',
		'load':'LOAD',
	},
	'awr-..-11\.2\.0\..*':{	
		
	}

}
osDetailArrayDelimMap = {
	'default':{
		'startPoint':'Statistics - detail',
		'endPoint':'-{15,}',
	}
}
osDetailArrayIndexMap = {
	'default':{
		'load':(1,2)
	}
}
#----------------------EN COURS DE DEV TODO implemmenter le mapper -------------------------------------------
def getOsInfos(fileString,version):
	"""Compatible with:
	11.0.2.* (test)
	Not compatible with  9.2"""
	delimMapper = mapper.Mapper(osArrayDelimMap,version) 
	indexMapper = mapper.Mapper(osArrayStartMap,version)
	if version.startswith("9.2"): 
		return None
	start = fileString.find(delimMapper.get('startPoint'))
	if(start == -1):
		return None
	start = fileString.find("\n",start)
	start = fileString.find(delimMapper.get('startPointBis'),start)
	start = fileString.find("\n",start)
	end = fileString.find(delimMapper.get('endPoint'),start)
	array = parse.getArrayFromDelim(fileString[start:end])
	allInfosArray = dict()
	
	for row in array:
		propertyName = row[0]
		if propertyName:
			allInfosArray[propertyName] = parse.toFloat(row[1])
	totalTime = allInfosArray[indexMapper.get('busyTime')]+allInfosArray[indexMapper.get('idleTime')]
	osInfo = dict()

	for key,index in indexMapper.iteritems():
		if index in allInfosArray :
			osInfo[key] = (allInfosArray[index]*100) / totalTime
	#----------------Details--------------
	delimMapper = mapper.Mapper(osDetailArrayDelimMap,version) 
	start = fileString.find(delimMapper.get('startPoint'))
	end = fileString.find(delimMapper.get('endPoint'),start)
	if start != -1:
		array = parse.getArrayFromDelim(fileString[start:end])
		indexMapper = mapper.Mapper(osDetailArrayIndexMap,version)
		for key,indexTuple in indexMapper.iteritems() : 
			osInfo[key] = parse.toFloat(array[indexTuple[0]][indexTuple[1]])

	#------------------------------------
	#----------------Memory----------------------------
	start = fileString.find("Shared Pool Statistics")
	end = fileString.find("\n\n",start)
	substring = fileString[start:end]
	array = parse.getArrayFromDelim(substring.replace('~',' '),"\n\n")
	osInfo['memoryUsage'] = ( parse.toFloat(array[0][0]) + parse.toFloat(array[0][1]) )/2.0 							#can be moved to general Infos 
	return osInfo