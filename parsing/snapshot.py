import re # Library  for regexp
import request
import event
import osInfo
import efficiency
import statistics
import loadProfile
import parsingTools as parse
import mapper
import json #Library for encoding a json
import logging
import ioStats
import instanceState
import parameters
import advisory
NB_TOP_REQUEST = 90

# snapTimeMetadataArrayMap={
# 	'default':{
# 		'startWith':'Snap Id',
# 		'begin':'Begin',
# 		'end':'End',
# 		'date_index':3,
# 		'time_index':4
# 	}
# }
# databaseMetadataArrayMap={
# 	'default':{
# 		'startWith':'Database',
# 		'dataBaseName_index':0,
# 		'dataBaseId_index':1,
# 		'instanceName_index':2,
# 		#'version_index':5,
# 		'isRac_index':6,
# 		'hostName_index':None
# 	}
# }
# hostMetadataArrayMap={
# 	'default':{
# 		'startWith':'Host',
# 		'hostName_index':1,
# 		'platform_index':2,
# 		'nb_cpu':3,
# 		'nb_core':4,
# 		'nb_sockets':5,
# 		'memory':6
# 	}
# }


def getSnapshot(fileString,version):
	logging.basicConfig(filename='snapshots.log',level=logging.DEBUG)
	"""return an object containing a snapshot from a statspack/awr containing a bunch of infos about the state of a dataBase"""
	snapshot = initialiseSnapshot(fileString,version)
	snapshot['EfficiencyIndicator'] = efficiency.getEfficencyIndicator(fileString,version)
	snapshot['Event'] = event.getEvents(fileString,version)
	snapshot['LoadProfile'] = loadProfile.getLoadProfile(fileString,version)
	snapshot['SqlInfo'] = request.getSQLInfos(fileString,version)
	snapshot['TableSpace'] = ioStats.getTableSpaceIoStats(fileString,version)
	snapshot['InstanceState'] = instanceState.getInstanceState(fileString,version)
	snapshot['Parameters'] = parameters.getParameters(fileString,version)
	snapshot['Stats'] = statistics.getStatistics(fileString,version)
	snapshot['OSState'] = osInfo.getOsInfos(fileString,version)
	snapshot['Advisory'] = advisory.getLimitedAdvisory(fileString,version)
	return snapshot


def initialiseSnapshot(fileString,version):
	"""Get infos like date and duration
		create snapshot with basic info 
		startDate // la date (jj-mm-aa hh-mm-ss) de debut du snap
		
	"""
	#------------------------------------------------------------#
	start = fileString.find("Snap Id")
	end = fileString.find("\n\n",start)
	substring = fileString[start:end]
	snapshot = dict()
	content = substring.split('\n')
	for row in content:
		row = row.split()
		if row[0] == "Begin":
			snapshot['startDate'] =  row[3]+" "+ row[4]
		if row[0] == "End":
			snapshot['endDate'] =  row[3]+" "+ row[4]
	#---------------------Get dbName---------------------------#
	start = fileString.find('Parameter Name')
	start = fileString.find('db_name',start)
	end = fileString.find("\n",start)
	splittedRow = fileString[start:end].split()
	snapshot['dataBaseName'] = splittedRow[1]
	#---------------------------------------------------------#
	start = fileString.find('Instance')
	end = fileString.find("\n\n",start)
	array = parse.getArrayFromDelim(fileString[start:end])
	snapshot['instanceName'] = array[0][2]

	snapshot['version'] = version
	return snapshot	


#-------------------------TODO: move in modules----------------------------#
def getTableSpaceIOStats(fileString,version):
	"""return an array containing all the tablespace and some infos about the Input/Output on the disk related
		for 11+ (TODO test) it also containing an histogram with the read time classed by values (0-2,2-4,4-8,8-16,16-32,32+)
	"""
	return getTableSpaceIOStats_(fileString)

def getTableSpaceIOStats_(fileString):
	"""
		compatible *, but return an histogram only for 10(11)+ (TODO test)
	"""
	start = fileString.find("Tablespace IO Stats")
	start = fileString.find("----",start)
	for i in range(4):		
		start = fileString.find("\n",start)+1
	end = fileString.find("----",start)+1
	substring = fileString[start:end]

	ioOnTableSpace = dict()
	rows = substring.split('\n')
	tableSpaceName=''
	for rowId in range(0,len(rows)-1,2):
		tableSpaceName = rows[rowId].strip()
		ioOnTableSpace[tableSpaceName] = dict()
		content = rows[rowId+1].split()
		ioOnTableSpace[tableSpaceName]['name'] = tableSpaceName
		ioOnTableSpace[tableSpaceName]['readS'] = parse.toFloat(content[0])
		ioOnTableSpace[tableSpaceName]['avRd'] = parse.toFloat(content[2])
	start = fileString.find("File Read Histogram Stats")
	if start != -1:
		start = fileString.find("----",start)
		start = fileString.find("\n\n",start)
		for i in range(3):
			start = fileString.find("\n",start)+1
		end = fileString.find("\n\n",start)
		substring = fileString[start:end]

		for rowId in range(0,len(rows)-1,2):
			tableSpaceName = rows[rowId].strip()
			if ioOnTableSpace[tableSpaceName] :
				#ioOnTableSpace[tableSpaceName]['fileName'] = rows[rowId].split()
				content = rows[rowId+1].split()
				ioOnTableSpace[tableSpaceName]['histogram'] = []
				ioOnTableSpace[tableSpaceName]['histogram'].append(parse.toFloat(content[0]))
				ioOnTableSpace[tableSpaceName]['histogram'].append(parse.toFloat(content[1]))
				ioOnTableSpace[tableSpaceName]['histogram'].append(parse.toFloat(content[2]))
				ioOnTableSpace[tableSpaceName]['histogram'].append(parse.toFloat(content[3]))
				ioOnTableSpace[tableSpaceName]['histogram'].append(parse.toFloat(content[4]))
				ioOnTableSpace[tableSpaceName]['histogram'].append(parse.toFloat(content[5]))

	tableSpaceList = []
	for key, value in ioOnTableSpace.iteritems():
		tableSpaceList.append(value)

	return tableSpaceList


def getEfficencyIndicator(fileString):
	"""
		return an associative array containing some indicator about the efficiency
		Compatible(TODO test):
		*
	"""
	efficiencyIndicator = dict()
	start = fileString.find("Efficiency")
	end = fileString.find("\n\n",start)
	substring = fileString[start:end]
	#find digit :
	# https://simple-regex.com/build/5835baa379d42
	# begin with any of (digit) once or more,
	# literally ".",
	# any of (digit) once or more,
	# must end, case insensitive
	numbers = re.findall('((?:[0-9])+(?:\.)(?:[0-9])|#{3,})',substring)
	efficiencyIndicator['bufferNoWait'] = parse.toFloat(numbers[0])
	efficiencyIndicator['redoNoWait'] = parse.toFloat(numbers[1])
	efficiencyIndicator['bufferHit'] = parse.toFloat(numbers[2])
	efficiencyIndicator['optimalWA'] = parse.toFloat(numbers[3])
	efficiencyIndicator['libraryHit'] = parse.toFloat(numbers[4])
	efficiencyIndicator['softParse'] = parse.toFloat(numbers[5])
	efficiencyIndicator['execToParse'] = parse.toFloat(numbers[6])
	efficiencyIndicator['latchHit'] = parse.toFloat(numbers[7])
	efficiencyIndicator['cpuToParse'] = parse.toFloat(numbers[8])
	efficiencyIndicator['nonParseCpu'] = parse.toFloat(numbers[9])
	return efficiencyIndicator