<%@ CodePage=65001 Language="VBScript"%>
<%
'Option Explicit
Response.Buffer = True
%>
<!--#include file="config.asp"-->
<!--#include file="util.asp"-->
<!--#include file="io.asp"-->
<!--#include file="basexml.asp"-->
<!--#include file="commands.asp"-->
<!--#include file="class_upload.asp"-->
<%

If ( ConfigIsEnabled = False ) Then
	SendError 1, "This connector is disabled. Please check the ""editor/filemanager/connectors/asp/config.asp"" file"
End If

DoResponse

Sub DoResponse()
	Dim sCommand, sResourceType, sCurrentFolder

	' Get the main request information.
	sCommand = Request.QueryString("Command")

	sResourceType = Request.QueryString("Type")
	If ( sResourceType = "" ) Then sResourceType = "File"

	sCurrentFolder = GetCurrentFolder()

	' Check if it is an allowed command
	if ( Not IsAllowedCommand( sCommand ) ) then
		SendError 1, "The """ & sCommand & """ command isn't allowed"
	end if

	' Check if it is an allowed resource type.
	if ( Not IsAllowedType( sResourceType ) ) Then
		SendError 1, "Invalid type specified"
	end if

	' File Upload doesn't have to Return XML, so it must be intercepted before anything.
	If ( sCommand = "FileUpload" ) Then
		FileUpload sResourceType, sCurrentFolder, sCommand
		Exit Sub
	End If

	SetXmlHeaders

	CreateXmlHeader sCommand, sResourceType, sCurrentFolder, GetUrlFromPath( sResourceType, sCurrentFolder, sCommand)

	' Execute the required command.
	Select Case sCommand
		Case "GetFolders"
			GetFolders sResourceType, sCurrentFolder
		Case "GetFoldersAndFiles"
			GetFoldersAndFiles sResourceType, sCurrentFolder
		Case "CreateFolder"
			CreateFolder sResourceType, sCurrentFolder
	End Select

	CreateXmlFooter

	Response.End
End Sub

%>
