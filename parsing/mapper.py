import re # Library  for regexp
class Mapper(): # Class for map
	"""
		A mapper
	"""
	def __init__(self,dictMap,version='default'):	
		self._map = dictMap
		self._version = version
	def _select(self):
		mapper = self._map['default']
		for pattern, content in self._map.iteritems():
			if re.match(pattern, self._version):
				mapper.update(content)
		return mapper
	def get(self,key):
		"""
			String mapper 
		"""
		return self._select().get(key,None)
	def keys(self):
		return self._select().keys()
	def iteritems(self):
		return self._select().iteritems()
	def getVersion(self):
		return self._version