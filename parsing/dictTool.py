from collections import defaultdict
def add(dicts):
	"""implementation of the npe_method found at: 
	http://stackoverflow.com/questions/10461531/merge-and-sum-of-two-dictionaries
	"""
	ret = defaultdict(float)
	for d in dicts:
		for k, v in d.items():
			v =  v if v is float else 0
			ret[k] += v 
	return dict(ret)
def select(dict,filterDict):
	selected = False
	for filterName,filterValue in filterDict.iteritems() :
		if filterName in dict :
			if dict[filterName] > filterValue :
				return True
def merge(dicts):
	mergedDict = dict()
	for d in dicts :
		mergedDict.update(d)
	return mergedDict