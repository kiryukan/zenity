import re # Library  for regexp
import mapper # inclu dans le dossier
import parsingTools as parse # inclu dans le dossier

# PARSER for init.ora Parameters statspack
import sys #for argument from terminal

parametersStartMap={
	"default":{
		"start":"init\.ora Parameters (.*\n){0,6}(?=-{10,})", 
		"break":".*init\.ora(.|\n)*Parameter Name.*\n(-*\s)*",
		"end":"-{40,}(.)*\n\n",
	},
	'awr-..-11\.1\.0\.*':{

	},
	'sp-..-12\.1.*':{}
}
def getParameters(fileString,version):
	"""
		Compatible(test TODO):
		*
	"""
	parametersStartMapper = mapper.Mapper(parametersStartMap,version)
	parametersString = parse.getSubString(fileString,parametersStartMapper.get("start"),parametersStartMapper.get("end")) 
	parametersString = re.sub(parametersStartMapper.get("break"),"",parametersString)
	parametersArray = parse.getArrayFromDelim(parametersString) 
	key=""
	parametersMap = {}
	for parameter in parametersArray :
		if parameter[0] != "" : 
			key = parameter[0] 
			parametersMap[key] = {} 
			parametersMap[key]["value"] = "" 
			parametersMap[key]["name"] = key 
		if parameter[1] != "": 
			parametersMap[key] 
			parametersMap[key]["value"] += parameter[1]

	return parametersMap.values()

