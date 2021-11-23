#encoding	
import re # Library  for regexp
import mapper
import parsingTools as parse
import dictTool
import logging
eventFilter = {
		#  over a certain value
		'total':0.1,
		'perSec':0.01,
		'perTrans':0.1
}
delimPointMap={
	"default":{
		"start_key":"Key Instance Activity Stats",
		"start_other":"Other Instance Activity Stats",
		"start_all":None,
		"end_key":"-{40,}",
		"end_other":"-{40,}",
		"end_all":"-{40,}",
		"subArraySeparator":"Instance Activity Stats(.*|\n)*\nStatistic.*\n-{10,}(-| )*\n"
	},
	'awr-wi-11\.2\.0\..*':{
		"start_key":None,
		"start_other":None,
		"start_all":"Instance Activity Stats",
	},
	'awr-wi-10\.2\.0\..*':{
		"start_key":None,
		"start_other":None,
		"start_all":"Instance Activity Stats",
	},
	'sp-..-11.*':{
		"start_key":None,
		"start_other":None,
		"start_all":"Instance Activity Stats"
	},
	'sp-..-10.*':{
		"start_key":None,
		"start_other":None,
		"start_all":"Instance Activity Stats"
	},
	'sp-..-12\.1.*':{
		"start_key":None,
		"start_other":None,
		"start_all":"Instance Activity Stats",
	}
}

indexMap={
	"default":{
		"name":0,
		"total":1,
		"perSec":2,
		"perTrans":3
	},
	'awr-wi-11\.1\.0\..*':{
	},
}
derivedStatIndexMap={
	"default":{
		"name":0,
		"total":1,
		"perHour":2
	}
}
derivedStatDelimMap={
	"default":{
		"start":"Statistics identified by '\(derived\)'.*\n(.*|\n)*\nStatistic.*\n",
		"end":"-{45,}",
		"subArraySeparator":"Instance Activity Stats(.*|\n)*\nStatistic.*\n-{10,}(-| )*\n"
	}
}
def getStatistics(fileString,version):
	delimPointMapper = mapper.Mapper(delimPointMap,version)
	indexMapper = mapper.Mapper(indexMap,version)
	statisticsArray = []
	if delimPointMapper.get('start_all') is not None:
		subarrayText = parse.getSubString(fileString,delimPointMapper.get('start_all'),delimPointMapper.get('end_all'),re.DOTALL)
		if subarrayText is not None:
			subarrayText = re.sub('\f','',subarrayText)
			subarrayText = re.sub(delimPointMapper.get('subArraySeparator'),'', subarrayText)
			statisticsArray.extend(parse.getArrayFromDelim(subarrayText))
	if delimPointMapper.get('start_key') is not None:
		subarrayText = parse.getSubString(fileString,delimPointMapper.get('start_key'),delimPointMapper.get('end_key'))
		if subarrayText is not None:
			subarrayText = re.sub('\f','',subarrayText)
			subarrayText = re.sub(delimPointMapper.get('subArraySeparator'),'', subarrayText)
			statisticsArray.extend(parse.getArrayFromDelim(subarrayText))
	if delimPointMapper.get('start_other') is not None:
		subarrayText = parse.getSubString(fileString,delimPointMapper.get('start_other'),delimPointMapper.get('end_other'),re.DOTALL)
		if subarrayText is not None:
			subarrayText = re.sub('\f','',subarrayText)
			subarrayText = re.sub(delimPointMapper.get('subArraySeparator'),'', subarrayText)
			statisticsArray.extend(parse.getArrayFromDelim(subarrayText))
	statistics = []
	filter = eventFilter
	for row in statisticsArray :
		statistic = {}
		statistic['name'] = row[indexMapper.get('name')][:30]
		for key,index in indexMapper.iteritems():
			if(index is not None):
				value = parse.toFloat(row[index])
				if (value is not None and key != ""):
					statistic[key] = value 
		for key,filterValue in eventFilter.iteritems():
			if key in statistic and statistic[key] >= filterValue :
				statistics.append(statistic)
				break;
	statistics.extend(getDerivedStatistics(fileString,version))
	
	return statistics
def getDerivedStatistics(fileString,version):

	delimPointMapper = mapper.Mapper(derivedStatDelimMap,version)
	indexMapper = mapper.Mapper(derivedStatIndexMap,version)
	statisticsArray = []
	if delimPointMapper.get('start') is not None:
		subarrayText = parse.getSubString(fileString,delimPointMapper.get('start'),delimPointMapper.get('end'),re.DOTALL)
		if subarrayText is not None:
			subarrayText = re.sub('\f','',subarrayText)
			subarrayText = re.sub(delimPointMapper.get('subArraySeparator'),'', subarrayText)
			statisticsArray = parse.getArrayFromDelim(subarrayText)
	statistics = []
	filter = eventFilter
	for row in statisticsArray :
		statistic = {}
		statistic['name'] = row[indexMapper.get('name')][:30]
		if statistic['name'] != None and statistic['name'] != '':
			for key,index in indexMapper.iteritems():
				if(index is not None):
					value = parse.toFloat(row[index])
					if (value is not None and key != ""):
						statistic[key] = value 
			statistics.append(statistic)
	return statistics