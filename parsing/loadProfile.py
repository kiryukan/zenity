import re # Library  for regexp
import mapper
import parsingTools as parse
import logging

loadProfileStartMap={
	"default":{
		"startPoint":"Load Profile",#Instance Efficiency
		"dbTime":"DB Time(s)",
		"redoSize":"Redo size",
		"logicalRead":"Logical read",
		"blockChange":"Block changes",
		"physicalRead":"Physical read",
		"physicalWrite":"Physical write",
		"userCalls":"User calls",
		"parses":"Parses",
		"hardParses":"Hard parses",
		"logons":"Logons",
		"execute":"Executes",
		"rollbacks":"Rollbacks",
		"transactions":"Transactions",
		"endPoint":"\n\n"
	},
	'awr-..-11\.1\.0\..*':{

	},
}
loadProfileCollumnIndexMap = {
	"default":{
		"perSec":1
	}
}
def getLoadProfile(fileString,version):
	arrayMapper = mapper.Mapper(loadProfileStartMap,version)
	collumnMapper = mapper.Mapper(loadProfileCollumnIndexMap,version)
	loadProfile = dict()
	start = fileString.find(arrayMapper.get("startPoint"))
	end = fileString.find(arrayMapper.get("endPoint"),start)
	array = fileString[start:end]
	for key,value in arrayMapper.iteritems() : 
		if(key != "startPoint" and key != "endPoint") :
			line = parse.getLineStartingWith(array,value)
			if line != "" :
				line = re.split(r'\s{2,}',line)
				loadProfile[key] = parse.toFloat(line[collumnMapper.get("perSec")])
				if(loadProfile[key] == None):
					loadProfile[key] = 0
	return loadProfile