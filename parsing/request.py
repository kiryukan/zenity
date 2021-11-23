import re # Library  for regexp
import mapper
import parsingTools as parse
import dictTool
import logging
import json #Library for encoding a json

NB_TOP_REQUEST = 90
arrayIndexMap={
	'byCpu':{
		'default':{
			'cpuTime':0,
			'exec':1,
			'cpuPerExec':2,
			'totalCpu':3,
			'elapTime':4,
			'cpu':5,
			'io':6,
			'sqlId':7,
			'bufferGet':None,
			'hash':None,

		},			
		'awr-..-10\.2\..*':{
			'elapTime':1,
			'exec':2,
			'cpuPerExec':3,
			'totalCpu':4,
			'sqlId':5,
			'totalCallTime':None
		},
		'awr-..-11\.2\.0\..*':{},
		'awr-..-11\.1\.0\..*':{},
		'sp-..-11\.2\.0\..*':{

			'bufferGet':5,
			'io':None,
			'sqlId':None,
			'hash':6
		},
		'sp-..-12\..*':{
			'bufferGet':5,
			'hash':6,
			'cpu':None,
			'io':None,
			'sqlId':None,
		},
	},
	'byGet':{
		'default':{
			'bufferGet':0,
			'exec':1,
			'getPerExec':2,
			'totalGets':3,
			'elapTime':4,
			'cpu':5,
			'io':6,
			'sqlId':7,
			'hash':None
		},
		'awr-..-10\.2\..*':{
			'cpuTime':4,
			'elapTime':5,
			'sqlId':6,
			'totalCallTime':None
		},
		'awr-..-11\.2\.0\..*':{},
		'sp-..-11\.2\.0\..*':{
			'totalGets':3,
			'cpuTime':4,
			'elapTime':5,
			'hash':6,
			'cpu':None,
			'io':None,
			'sqlId':None,

		},
		'sp-..-12\..*':{
			'cpu':4,
			'elapTime':5,
			'hash':6,
			'io':None,
			'sqlId':None,
		},
	},
	'byExec':{
		'default':{
			'exec':0,
			'rowProcessed':1,
			'rowPerExec':2,
			'elapTime':3,
			'cpu':4,
			'io':5,
			'sqlId':6,
			'cpuPerExec':None,
			'elapPerExec':None,
			'hash':None,
		},
		'awr-..-10\.2\..*':{
			'rowPerExec':3,
			'cpuPerExec':4,
			'elapPerExec':5,
			'elapTime':None,
			'cpu':None,
			'io':None,
			'sqlId':6
		},
		'awr-..-11\.2\.0\..*':{},
		'sp-..-11\.2\.0\..*':{
			'cpuPerExec':3,
			'elapPerExec':4,
			'hash':5,
			'cpu':None,
			'io':None,
			'sqlId':None,
			'elapTime':None

		},
		'sp-..-12\..*':{
			'cpuPerExec':3,
			'elapPerExec':4,
			'hash':5,
			'cpu':None,
			'io':None,
			'sqlId':None,
		}
	},
	'byParse':{
		'default':{
			'parseCall':0,
			'exec':1,
			'totalParse':2,
			'sqlId':3
		},
		'awr-..-10\.2\..*':{},
		'awr-..-11\.2\.0\..*':{},
		'sp.*':{
			'hash':3,
			'sqlId':None,
		},

	},

	'bySharableMemory':{
		'default':{
			'sharableMemory':0,
			'exec':1,
			'totalSharableMemory':2,
			'sqlId':3,
			'hash':None

		},
		'sp-..-11\.2\.0\..*':{
			'exec':3,
			'totalSharableMemory':4,
			'sqlId':None,
			'hash':5,
		},
		# 	'.*10\..*':{
		# 	'elapTime':1,
		# 	'exec':2,
		# 	'cpuPerExec':3,
		# 	'total':4,
		# 	'sqlId':5,
		# 	'totalCallTime':None
		# }
		'awr-..-10\.2\..*':{},
		'awr-..-11\.2\.0\..*':{},
		'sp-..-12\..*':{
			'sharableMemory':0,
			'exec':None,
			'totalSharableMemory':4,
			'sqlId':None,
			'hash':5
		}


	},
	'byVersionCount':{
		'default':{
			'maxVersionCount':0,
			'exec':1,
			'sqlId':2
		},
		'awr-..-10\.2\..*':{},
		'awr-..-11\.2\.0\..*':{},
		'sp-..-11\.2\.0\..*':{
			'exec':3,
			'sqlId':None,
			'hash':4
		},
	},
	'byElapTime':{
		'default':{
			'elapsedTime':0,
			'exec':1,
			'elapPerExec':2,
			'totalElapTime':3,
			'cpu':4,
			'io':5,
			'sqlId':6,
			'hash':None
		},
		'awr-..-10\.2\..*':{
			'cpu':1,
			'exec':2,
			'elapPerExec':3,
			'totalElapTime':4,
			'sqlId':5,
			'cpu':None,
			'io':None,
		},
		'awr-..-11\.2\.0\..*':{},
		'sp-..-11\.2\.0\..*':{
			'sqlId':None,
			'hash':6,
			'io':None
		},
		'sp-..-12\.*.*':{
			'hash':6,
			# 'io':Physical reads  
			'sqlId':None,
		}

	},
	'byReads':{
		'default':{
			'physicalReads':0,
			'exec':1,
			'readExec':2,
			'totalRead':3,
			'elapTime':4,
			'cpu':5,
			'io':6,
			'sqlId':7
		},
		'awr-..-10\.2\..*':{
			'cpu':None,
			'elapTime':5,
			'io':None,
			'sqlId':6
		},
		'sp-..-11\.2\.0\..*':{
			'elapTime':5,
			'hash':6,
			'sqlId':None,
			'io':None 
		},
	}
}
delimPointMap = {
	'default':{
		'delim':'SQL ordered by(.|\n)*\n-{5,}.*\n',
		'end':'-{25,}',
		'startBis':'-{5,}.*\n'
		}
}
startPointMap = {
	'default':{
		'byReads':'SQL ordered by Reads',
		'byElapTime':'SQL ordered by Elapsed time',
		'byCpu':'SQL ordered by CPU',
		'byGet':'SQL ordered by Gets',
		'byExec': 'SQL ordered by Executions',
		'byParse':'SQL ordered by Parse Calls',
		'bySharableMemory':'SQL ordered by Sharable Memory',
		'byVersionCount':'SQL ordered by Version Count',
	}
}

def getSQLInfos(fileString,version):
	"""Return an array containing some infos about the request executed in the dataBase
		Also containing an array with the top 5 Request executed 
	"""
	sqlInfos = dict()
	sqlInfos ['Request'] = getRequests(fileString,version)

	start = fileString.find("Shared Pool Statistics")
	end = fileString.find("\n\n",start)
	substring = fileString[start:end]
	array = parse.getArrayFromDelim(substring.replace('~',' '),"\n\n")
	sqlInfos['memoryUsage'] =  (parse.toFloat(array[1][0])+parse.toFloat(array[0][0]))/2.0
	sqlInfos['repeatedRequestPercent'] = (parse.toFloat(array[1][0])+parse.toFloat(array[1][1]))/2.0	
	sqlInfos['repeatedRequestMemory'] = (parse.toFloat(array[2][0])+parse.toFloat(array[2][1]))/2.0
	start = fileString.find('SQL ordered by Executions')
	start = fileString.find('Total Executions',start)
	end = fileString.find('\n',start)
	array = re.split('  +',fileString[start:end])
	if len(array) > 1:
		sqlInfos['nbExec'] 	= parse.toFloat(array[1])						
	return sqlInfos

def getRequests(fileString,version):
	"""Get all the requests from a statsPack/AWR and return a map containing all the indicator for the events"""
	startPointMapper = mapper.Mapper(startPointMap,version)
	delimPointMapper = mapper.Mapper(delimPointMap,version)
	endPoint = delimPointMapper.get('end')
	requestsArray = []
	for orderBy,startPoint in startPointMapper.iteritems() :
		requestArray = parse.getSubString(fileString,startPoint,endPoint)
		if requestArray is not None:
			requestArray = re.sub(delimPointMapper.get('delim'),'',requestArray)
			if requestArray is not None:
				indexMapper = mapper.Mapper(arrayIndexMap.get(orderBy),version)
				requestOrderBy = _getRequestFromArray(indexMapper,requestArray,orderBy)
				if requestOrderBy is not None :
					requestsArray.append(requestOrderBy)

	return dictTool.merge(requestsArray).values()



def _getRequestFromArray(indexMapper,requestString,orderBy = None):
	if requestString == None:
		return None 
	# print "-----------------------------------------------------------------------------------------------------"
	topRequests = dict()
	pattern = re.compile("([0-9]+([\.\,][0-9]+)* +){4,}")
	blocksStart = []
	# print requestString
	for match in re.finditer(pattern,requestString):
		blocksStart.append(match.start())
	nbRequests = NB_TOP_REQUEST if len(blocksStart) > NB_TOP_REQUEST else len(blocksStart)-1
	for blockIndex in range(nbRequests):
		request = dict()
		start = blocksStart[blockIndex]
		end = blocksStart[blockIndex+1]
		block = requestString[start:end]
		rows = block.split('\n')
		values = rows[0].split()
		request = dict()
		requestId = ''
		try :
			if(indexMapper.get('sqlId') is None) :
				index = indexMapper.get('hash')
				requestId = values[index]
				request['hash'] = requestId
			else :
				index = indexMapper.get('sqlId')
				requestId = values[index] # Pb out of index here
				request['sqlId'] = requestId
		except Exception :
			logging.exception('index('+str(index)+') of Request('+orderBy+').sqlId is not valid for '+ indexMapper.getVersion()
				+'\n\tValues:'+";".join(values))
		finally:
			for key,index in indexMapper.iteritems():
				if key != 'sqlId' and key != 'hash': 
					if(index is not None):
						try :
							value = parse.toFloat(values[index])
						except Exception :
							logging.exception('index('+str(index)+') of Request.'+str(key)+' is not valid for '+ indexMapper.getVersion()
								+'\n\tValues:'+";".join(values))
							return None
						if (value is not None):
							request[key] = value
			if rows[1].startswith('Module') :
				request['module'] = rows[1].replace("Module: ","")
				request['sqlCode'] = ("".join(rows[2:])).replace("\s+"," ")
			else:
				request['sqlCode'] = ("".join(rows[1:])).replace("\s+"," ")
			topRequests[requestId] = request
	return topRequests.items()
