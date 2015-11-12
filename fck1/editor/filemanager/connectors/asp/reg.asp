<%
Dim name
name = request.QueryString("name")
Set oRegex = New RegExp
oRegex.Global		= True
oRegex.Pattern = "(/\.)|(//)|([\\:\*\?\""\<\>\|]|[\u0000-\u001F]|\u007F)|(\.)"

if (oRegex.Test(name)) then
	response.write(name&" is can't bypass<br/>")
else
	response.write(name&" is bypass!!<br/>")
End if
%>