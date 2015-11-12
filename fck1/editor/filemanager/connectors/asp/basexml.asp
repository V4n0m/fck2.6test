<%

Sub SetXmlHeaders()
	' Cleans the response buffer.
	Response.Clear()

	' Prevent the browser from caching the result.
	Response.CacheControl = "no-cache"

	' Set the response format.
	on error resume next
	' The CodePage property isn't supported in Windows 2000. #2604
	Response.CodePage 		= 65001
	on error goto 0

	'Response.CharSet		= "UTF-8"
	Response.Addheader	"Content-Type","text/html;CharSet=UTF-8"
	Response.ContentType	= "text/xml"
End Sub

Sub CreateXmlHeader( command, resourceType, currentFolder, url )
	' Create the XML document header.
	Response.Write "<?xml version=""1.0"" encoding=""utf-8"" ?>"

	' Create the main "Connector" node.
	Response.Write "<Connector command=""" & command & """ resourceType=""" & resourceType & """>"

	' Add the current folder node.
	Response.Write "<CurrentFolder path=""" & ConvertToXmlAttribute( currentFolder ) & """ url=""" & ConvertToXmlAttribute( url ) & """ />"
End Sub

Sub CreateXmlFooter()
	Response.Write "</Connector>"
End Sub

Sub SendError( number, text )
	SetXmlHeaders

	' Create the XML document header.
	Response.Write "<?xml version=""1.0"" encoding=""utf-8"" ?>"

	If text <> "" then
	Response.Write "<Connector><Error number=""" & number & """ text=""" & Server.HTMLEncode( text ) & """ /></Connector>"
	else
	Response.Write "<Connector><Error number=""" & number & """ /></Connector>"
	end if

	Response.End
End Sub
%>
