import re # Library  for regexp
import mapper
import parsingTools as parse

tableSpaceArrayDelimMap={
		'default':{
			'startPoint':'Tablespace.*\n-{4,}(.|\n-)*-{5,}(\n[^-]*)',
			'endPoint':'-{40,}',
			'delim':'Tablespace(.|\n)*-{4,}\n',
			'head':'((-{4,}) *){4,}'
		},
		'awr-..-11\.2\.0\.3':{},
		'awr-..-10\.2\.0\.4':{},
		'sp-..-12\..*':{
			'startPoint':'Tablespace IO(.*\n){,4}Tablespace.*\n-{4,}(.|\n-)*-{5,}(\n[^-]*)',
		}
}
tableSpaceArrayIndexMap = {
	'default':{
		'readNb':0,
		'avReadS':1,
		'avRead':2,
		'avBlkReads':3,
		'writesNb':4,
		'avWritesS':5,
		'bufferWaits':6,
		'avBufferWaits':7,
	},
	'awr-..-11\.2\.0\.3.*':{	

	},
	'sp-..-11\.2.*':{	

	},
	'sp-..-12\.1.*':{

	}
}
def getTableSpaceIoStats(fileString,version):
	delimMapper = mapper.Mapper(tableSpaceArrayDelimMap,version)
	indexMapper = mapper.Mapper(tableSpaceArrayIndexMap,version)
	subString = parse.getSubString(fileString,delimMapper.get('startPoint'),delimMapper.get('endPoint'))
	head = re.search(delimMapper.get('head'),subString).group(0)
	subString = re.sub(delimMapper.get('delim'),'',subString)
	subString = re.sub(head+'\n','',subString)
	rows = subString.split('\n')[0:]
	ioOnTableSpace = dict()
	for rowId in range(0,len(rows)-1,2):
		tableSpaceName = rows[rowId].strip()
		ioOnTableSpace[tableSpaceName] = {}
		ioOnTableSpace[tableSpaceName]['name'] = tableSpaceName
		content = parse.getArrayFromDelim(head+'\n'+rows[rowId+1])[0]
		for key,index in indexMapper.iteritems():
			if index < len(content) :
				ioOnTableSpace[tableSpaceName][key] = parse.toFloat(content[index])
	tableSpaceList = []
	for key, value in ioOnTableSpace.iteritems():
		tableSpaceList.append(value)
	return tableSpaceList
