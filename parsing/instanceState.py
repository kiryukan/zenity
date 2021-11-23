# coding=utf-8
import re # Library  for regexp
import mapper
import parsingTools as parse
instanceActivityStartMap={
	"default":{
		"start":"Load Profile",#Instance Efficiency
		"redoLogSpaceRequests":"redo log space requests",
		"redoLogSpaceWaitTime":"redo log space wait time",
		"sqlAreaEvicted":"sql area evicted",
		"sqlAreaPurged":"sql area purged",
		"physicalRead":"Physical reads",
		"physicalWrite":"Physical write",
		"userCall":"User calls",
		"userCommit":"user commit",
		"userIOWaitTime":"user I/O wait time",
	},
	'awr-..-11\.1\.0\.*':{

	},
	'sp-..-12\.1.*':{}
}
globalInfoMap={
	"default":{
		"start":"Snap Id",#Instance Efficiency
		"end":"\n\n",
		"sessionStart_row":0,
		"sessionStart_col":2,
		"sessionEnd_row":1,
		"sessionend_col":2,
	},
	'awr-..-11\.1\.0\..*':{

	},
	'sp-..-12\.1.*':{
		# "sessionStart_row":0,
		# "sessionStart_col":3,
		# "sessionEnd_row":1,
		# "sessionend_col":3,
	}

	
}
def getInstanceState(fileString,version):
	"""
		get some general infos about the state of an instance 
		Compatible(test TODO):
		*
	"""
	instanceActivityMapper = mapper.Mapper(instanceActivityStartMap,version)
	start = fileString.find(instanceActivityMapper.get("start"))
	instanceState = dict()
	for key,startPoint in instanceActivityMapper.iteritems() :
		if key != 'start' :
			line = parse.getLineStartingWith(fileString[start:],instanceActivityMapper.get(key))
			if line :
				instanceState[key] = parse.toInt(re.split('  +',line)[1])

	#-----------------------------------------------------------------------------------#
	globalInfoMapper = mapper.Mapper(globalInfoMap,version)
	start = fileString.find(globalInfoMapper.get("start"))
	end = fileString.find(globalInfoMapper.get("end"),start)
	arrayStr = fileString[start:end].replace("~", " ")

	array = parse.getArrayFromDelim(arrayStr)
	row = globalInfoMapper.get("sessionStart_row")
	col = globalInfoMapper.get("sessionStart_col")
	nbSessionStart =  parse.toFloat(array[row][col])

	row = globalInfoMapper.get("sessionEnd_row")
	col = globalInfoMapper.get("sessionend_col")
	nbSessionEnd = parse.toFloat(array[row][col])

	if nbSessionStart is not None and nbSessionEnd is not None :
		instanceState['nbSession'] = int((nbSessionStart + nbSessionEnd )/ 2)

	return instanceState