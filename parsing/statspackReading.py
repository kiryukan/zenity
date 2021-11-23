import json #Library for encoding a json
import sys #for argument from terminal
import dataBase
import snapshot
import re
import os


def sendErrorMessage(message):
	print message

""" Read statspack and create JSON """
def readStatsPack(snapshotPath,fileName,clientName):
	"""Read a statspack and return a JSON
		"""
	message = ""
	index = 0
	file = open(snapshotPath+'/'+fileName) # open snapshot file
	fileString=file.read().decode('latin1') # read it
	statspack = dict()
	""" create a statspack with a timestamp,an instance name and a database name """
	fileName = fileName.split(os.sep)[-1]
	metaData = fileName.split('_') # split namefile separated by underscores
	versionNumber = re.search("((?:[0-9]{1,2}\.){4}[0-9]{1,2})", fileString).group(1) # search version of statsPack
	reportType = 'sp' if (re.search("STATSPACK", fileString,re.I)) else 'awr' # define SP FREE or AWR ORACLE PAYING pack
	reportOs = 'wi' if (re.search("\r\n", fileString,re.I)) else 'li' # define OS as windows or linux

	version = reportType+'-'+reportOs+'-'+versionNumber # compose complete version name
	if(clientName == ''): # get client name from filename
		clientName = metaData[0]
	fileString = re.sub("(\r\n|\n|\r)", "\n",fileString)
	fileString = re.sub('\x99|\xae',' ',fileString)
	
	# MAPPING STATSPACK INFOS TO -statspack- MAPPED ARRAY
	statspack['Base'] = dataBase.getDataBase(fileString)
	statspack['Base']['clientName'] = clientName
	statspack['Snapshot'] = snapshot.getSnapshot(fileString,version)
	statspack['Snapshot']['clientName'] = clientName
	statspack['Snapshot']['fileName'] = fileName
	statspack['Snapshot']['serverName'] = statspack['Base']['Instance'][0]['serverName'] # Create serverName...
	return json.dumps(statspack) # Return statspack object in JSON form

if len(sys.argv) == 4:
	print readStatsPack(sys.argv[1],sys.argv[2],sys.argv[3])
elif len(sys.argv) == 3:
	print readStatsPack(sys.argv[1],sys.argv[2],'')