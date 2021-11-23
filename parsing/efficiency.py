import re # Library  for regexp
import mapper
import parsingTools as parse
from collections import Counter
import dictTool
instanceEfficiencyIndexMap={
	"default":{
		"startPoint":"Efficiency",#Instance Efficiency
		"bufferNoWait":0,
		"redoNoWait":1,
		"bufferHit":2,
		"optimalWA":3,
		"libraryHit":4,
		"softParse":5,
		"execToParse":6,
		"latchHit":7,
		"cpuToParse":8,
		"nonParseCpu":9,
		"inMemorySort" : None,
		"endPoint":"\n\n"
	},
	'awr-..-10\.2\.0\..*':{
		"inMemorySort":3
	},
	'awr-..-11\.2\.0\.3.*':{
		"inMemorySort":3
	},
	'sp-..-11\.2\.0\..*':{	

	},
	'sp-..-12\.1.*':{

	}
}
pgaCacheHitMap={
	"default":{
		"startPoint":"PGA Cache Hit",
		"endPoint":"\n\n",
		"pgaCacheHit":0
	},
	"sp-..-11\.2\.0\..*":{	

	}
}


def getEfficencyIndicator(fileString,version):
	"""
		return an associative array containing some indicator about the efficiency
		Compatible(TODO test):
		*
	"""
	
	arrayMapper = mapper.Mapper(instanceEfficiencyIndexMap,version)
	efficiencyIndicator = dict()
	start = fileString.find(arrayMapper.get("startPoint"))
	end = fileString.find(arrayMapper.get("endPoint"),start)
	#find digit :
	# https://simple-regex.com/build/5835baa379d42
	# begin with any of (digit) once or more,
	# literally ".",
	# any of (digit) once or more,
	# must end, case insensitive
	numbers = re.findall('((?:[0-9])+(?:\.)(?:[0-9])|#{3,}|(?<=:) {4,})',fileString[start:end])
	for key,value in arrayMapper.iteritems() : 
		if(key != "startPoint" and key != "endPoint" and value != None) :
			efficiencyIndicator[key] = parse.toFloat(numbers[value])

	# arrayMapper = mapper.Mapper(pgaCacheHitMap,version)
	# start = fileString.find(arrayMapper.get("startPoint"))
	# end = fileString.find(arrayMapper.get("endPoint"),start)
	# parse.getArrayFromDelim(fileString[start:end],end)
	# for key,value in arrayMapper.iteritems() : 
	# 	if(key != "startPoint" and key != "endPoint" and value != None) :
	# 		efficiencyIndicator[key] = parse.toFloat(numbers[value])

	return efficiencyIndicator
