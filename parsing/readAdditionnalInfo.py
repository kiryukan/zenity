def readAdditionnalInfo(path,fileName,statspackDict):
	file = open(path+'/'+fileName)
	fileString=file.read().decode('latin1')
	fileName = fileName.split(os.sep)[-1]
	clientName = fileName.split('_')[0]
	additionnalInfo = xmltodict.parse(fileString)
	additionnalInfo['clientName'] = clientName
	return xmltodict.parse(fileString)
