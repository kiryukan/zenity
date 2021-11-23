import re # Library  for regexp
import sys #for argument from terminal
import json #Library for encoding a json 
import parsingTools as parse
import advisory
def getDataBase(fileString):
	"""Get the database from a statsPack"""
	dataBase = initialiseDatabase(fileString)
	version = dataBase['version']
	dataBase['Instance'] = [getInstance(fileString,version)]
	return dataBase

def initialiseDatabase(fileString):
	dataBase = dict()
	start = fileString.find('Parameter Name')
	# end = fileString.find('\n\n',start)
	substring = fileString[start:]

	start = substring.find('db_name')
	end = substring.find("\n",start)
	row = substring[start:end]
	splittedRow = row.split()
	dataBase['name'] = splittedRow[1]
	dataBase['version'] = getVersion(fileString)

	dataBase['sgbd'] = "oracle"
	return dataBase
def getVersion(fileString):
	return re.search("([0-9]+\.){4}[0-9]",fileString).group()

def getInstance(fileString,version):
	""""""
	instance = initialiseInstance(fileString)
	instance['SgbdConfig'] = getSGBDConfig(fileString,version)
	instance['Advisory'] = advisory.getAdvisory(fileString,version)
	instance['ServerConfig'] = getServerConfig(fileString,version)
	return instance
#-------------------------------------ServerConfig------------------------------------
def getServerConfig(fileString,version):
	"""get the infos about the hardware of the instance
	Compatible(TODO : test):
	11.*"""
	serverConfig = dict()
	start = fileString.find("Host")
	if start == -1:
		return None
	else :
		end = fileString.find("\n\n",start)
		substring = fileString[start:end]
		substring = substring.replace("~"," ")
		array = parse.getArrayFromDelim(substring)
		if (array != None):
			serverConfig['os'] = parse.toFloat(array[0][1])
			serverConfig['nbCpu'] = parse.toFloat(array[0][2])
			serverConfig['nbCore'] = parse.toFloat(array[0][3])
			serverConfig['memory'] = parse.toFloat(array[0][5])
			# serverConfig['isRac'] = True if array[0][6] == "YES" else False
		return serverConfig
#-----------------------------SGBDCONFIG------------------------------------------
def getSGBDConfig(fileString,version):
	"""get the infos about the configuration of oracle (mainly tuning of the memory (oracleConfig))"""
	sgbdConfig=dict()
	if version.startswith("11"):
		sgbdConfig['OracleConfig'] = getOracleConfig_11(fileString,version)
	elif version.startswith("10") :
		sgbdConfig['OracleConfig'] = getOracleConfig_10(fileString,version)
	elif version.startswith("9"):
		sgbdConfig['OracleConfig'] = getOracleConfig_9(fileString,version)
	return sgbdConfig

def getOracleConfig_10(fileString,version):
	"""Compatible(TODO test):10.*"""
	oracleConfig = dict()
	start = fileString.find("Cache Size")
	for x in xrange(2):
		start = fileString.find("\n",start)+1
	end = fileString.find("\n\n",start)
	substring = fileString[start:end]
	substring = substring.replace("~"," ")
	try:
		array = parse.getArrayFromDelim(substring)
	except AttributeError:
		array = []
		for subArray in re.split(r'\n',substring):
			array.append(re.split(r'  +',subArray.lstrip()))

	if array is not None:
		oracleConfig['bufferCacheSize'] = parse.convertToM(array[0][1])
		oracleConfig['sharedPoolSize'] = parse.convertToM(array[1][1])


	start = fileString.find("SGA Memory")# TODO IMPORTANT FAIRE LA SOMME
	start = fileString.find("Variable Size",start)
	if start != -1:
		end = fileString.find("\n",start)
		oracleConfig['sgaSize'] = parse.convertToM(re.split(r'\s{2,}', fileString[start:end])[1],"B")
	start = fileString.find("Parameter Name",start)
	start = fileString.find("java",start)
	if start != -1:
		end = fileString.find("\n",start)
		oracleConfig['javaPoolSize'] = parse.convertToM(re.split(r'\s{2,}', fileString[start:end])[1],"B")	

	start = fileString.find("large",start)
	if start != -1:
		end = fileString.find("\n",start)
		oracleConfig['largePoolSize'] = parse.convertToM(re.split(r'\s{2,}', fileString[start:end])[1],"B")	
	
	start = fileString.find("session pga memory")
	if start != -1:
		end = fileString.find("\n",start)
		oracleConfig['pgaSize'] = parse.convertToM(re.split(r'\s{2,}', fileString[start:end])[1],"B")	
	return oracleConfig

def getOracleConfig_11(fileString,version):
	"""
	Compatible(TODO test):11.*
	"""
	oracleConfig = dict()
	start = fileString.find("Memory Dynamic Components")
	if start == -1 :
		return
	start = fileString.find("\n\n",start)
	end = fileString.find("----------------------------------------------------",start)
	substring = fileString[start:end]
	array = parse.getArrayFromDelim(substring)
	oracleConfig['sgaSize'] = parse.toFloat(array[4][1])
	oracleConfig['bufferCacheSize'] = parse.toFloat(array[0][1])
	oracleConfig['sharedPoolSize'] = parse.toFloat(array[5][1])
	oracleConfig['pgaSize'] = parse.toFloat(array[3][1])
	oracleConfig['largePoolSize'] = parse.toFloat(array[2][1])
	oracleConfig['javaPoolSize'] = parse.toFloat(array[1][1])
	oracleConfig['sgaAuto'] = True if array[3][4] == "STATIC" else False 
	oracleConfig['pgaAuto'] = True if array[4][4] == "STATIC" else False
	return oracleConfig

def getOracleConfig_9(fileString,version):
	"""Compatible: 
	statsPack 9.2.0.7.0: sp_SIAM_20160723_10h.lst
	"""
	oracleConfig = dict()
	start = fileString.find("Cache Size")
	for x in xrange(2):
		start = fileString.find("\n",start)+1#saute 2 lignes
	end = fileString.find("\n\n",start)
	substring = fileString[start:end]
	array = substring.split()
	oracleConfig['bufferCacheSize'] = parse.convertToM(array[2])
	oracleConfig['sharedPoolSize'] = parse.convertToM(array[10])


	start = fileString.find("SGA Memory")
	start = fileString.find("sum",start)
	end = fileString.find("\n",start)

	oracleConfig['sgaSize'] = parse.convertToM(fileString[start:end].split()[1],"B")
	start = fileString.find("Parameter Name",start)
	start = fileString.find("java",start)
	end = fileString.find("\n",start)
	try:
		oracleConfig['javaPoolSize'] = parse.convertToM(re.split(r'\s{2,}', fileString[start:end])[1],"B")	
	except:
		oracleConfig['javaPoolSize'] = None
	start = fileString.find("large",start)
	if start != -1:
		end = fileString.find("\n",start)
		oracleConfig['largePoolSize'] =  parse.convertToM(re.split(r'\s{2,}', fileString[start:end])[1],"B")	
		
	start = fileString.find("session pga memory")
	end = fileString.find("\n",start)
	try:
		pgaSize = re.split(r'\s{2,}',fileString[start:end])[3]
		oracleConfig['pgaSize'] = parse.convertToM(pgaSize,"B")	
	except IndexError:
		oracleConfig['pgaSize'] = None
	return oracleConfig

#-------------------------------------------------------------------------------------
def initialiseInstance(fileString):
	"""
	Return an empty array containing the instance and her name
	Compatible(TODO test):
		11.2.0.1"""
	instance = dict()
	start = fileString.find("report for")
	start = fileString.find("\n\n",start)+1
	end = fileString.find("\n\n",start)

	array = parse.getArrayFromDelim(fileString[start:end],"\n\n")
	instance['name'] = array[0][2]
	if(fileString[start:end].find("Host") != -1):
		instance['serverName'] = array[0][6]
	else:
		start = fileString.find("Host")
		end = fileString.find("\n\n",start)
		array_string = fileString[start:end]
		array = parse.getArrayFromDelim(array_string,"\n\n")
		if array == [] :
			array = array_string.split()
			instance['serverName'] = array[2]
		else :
			instance['serverName'] = array[0][0] if (array[0][0] != "") else array[0][1]
	return instance
	#start = collDelim[0]+collDelim[1]+2
	#instance['name'] = row[start:start+collDelim[2]].strip()
	#start = start + collDelim[2]+1
