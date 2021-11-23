import re # Library  for regexp

def getColdelim(substring):
	"""get an array containing the size of each - or ~ sequence and returning an array containing those size"""
	start = 0
	start = re.search("[~-]{2}",substring).start()
	end = substring.find("\n",start)
	substring = substring[start:end]

	collDelim = re.findall('(?:[\-~])+',substring)
	collDelim = [len(item) for item in collDelim]
	# console.log("collDelim vaut: "+collDelim )
	return collDelim

def getArrayFromDelim(substring,endDelim="---"):
	"""Get an 2d array from a human readable array"""
	match = re.search(" *[-~]{2,}",substring)
	if(match == None):
		return None
	start = match.start()	#start at a row with space followed by a 2 ~ or 2 -
	substring = substring[start:]
	collDelim = getColdelim(substring)
	match =  re.search("[-~]{2,}",substring)
	if(match == None):
		return None
	start = match.start()
	startColl = start 									# save the position of where the collumn start 
	start = substring.find("\n",start)
	end = substring.find(endDelim,start)
	substring = substring[start:end]
	rows = substring.split('\n')
	array = []
	rowId = 0
	for row in rows :
		if row:
			array.append([])#on rajoute une ligne 
			start = startColl #l'indice actuel de collonne
			collId = 0
			for collSize in collDelim:
				array[rowId].append(row[start:start+collSize].strip())
				start += collSize+1
			rowId += 1
	return array

def toCamelCase(snake_str):
	"""change a string from snake_case to the camelCase"""
	snake_str = snake_str.lower()
	components = snake_str.split('_')
	# We capitalize the first letter of each component except the first one
	# with the 'title' method and join them together.
	return components[0] + "".join(x.title() for x in components[1:])

def getLineStartingWith(strToRead,startWith,endDelim = "\n"):
	"""return a string containing the line beginning by startWith from strToRead, """
	start = strToRead.find(startWith)
	end = strToRead.find(endDelim,start)
	return strToRead[start:end]

def convertToM(strWithUnit,unit=None):
	"""Convert a string containing a number with his unit (if the unis isn't here we can pass it to 
		the second parameter) to a float containing the conversion of this digit in megaByte
	"""
	strWithUnit = strWithUnit.replace(",","")
	conversionTable = {
		"B": 9.53674316e-7,
		"K": 0.00098,
		"M": 1,
		"G": 1024
	}
	number = re.search("(?:[0-9\.])+",strWithUnit).group()
	if unit == None:
		unit = re.search("["+"".join(conversionTable.keys())+"]",strWithUnit).group()
	number = float(number) * conversionTable[unit]
	return float('%.2f' % number)#round it to 2 digit after the floating point

def toFloat(strNmbr):
	"""convert a string in american format in a floating point number"""
	try :
		return float(strNmbr.replace(",","").strip())
	except ValueError:
		return None

def toInt(strNmbr):
	"""convert a string in american format in an integer"""
	try :
		return int(strNmbr.replace(",","").strip())
	except ValueError:
		return None

def getSubString(string,startString,endString = '\n\n',startIndex = 0):
	match = re.search(startString,string[startIndex:],re.IGNORECASE)
	if match : 
		start = match.end()
	else :
		return None 
	if endString != None:
		match = re.search(endString,string[start:])
		if match : 
			end = match.start()
		else :
			return None 
		return string[start:start+end]
	else :
		return string[start:]
	#matches = re.search(startString+'(?P<content>(\n|.)*'+endString+')',string[0:])