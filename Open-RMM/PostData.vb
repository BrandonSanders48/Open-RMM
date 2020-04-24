Option Infer On
Imports System.IO
Imports System.Net
Imports System.Text
Imports System.Diagnostics
Imports MySql.Data.cf
Module PostData
    Public db_username As String = ""
    Public db_password As String = ""
    Public db_server As String = ""
    Public db_database As String = ""
    Public db_timeout As String = ""

    Public Function POST(hostname As String, className As String, jsonData As String) As String
        Dim sql As String
        Dim connection As New MySqlConnection("server=" & db_server & ";" & "user=" & db_username & ";" & "password=" & db_password & ";" & "database=" & db_database & ";Connection Timeout=" & db_timeout)
        connection.CreateCommand.CommandTimeout = 600000
        Try
            connection.Open()
        Catch myerror As MySqlException
            log("DB Open Failed" & myerror.Message)
        End Try
        Try
            jsonData = jsonData.Replace("'", "")
            jsonData = jsonData.Replace("\", "\\")

        Catch myerror As MySqlException
            log("Json Data Replace Failed" & myerror.Message)
        End Try
        Try
            If className = "ServiceStart" Then
                sql = "INSERT IGNORE INTO computerdata (hostname)VALUES('" & hostname & "')"
            Else
                sql = "INSERT INTO wmidata (Hostname,WMI_Name,WMI_Data, last_update)VALUES('" & hostname & "','" & hostname & "|" & className & "','" & jsonData & "', '" & My.Computer.Clock.LocalTime & "')"
            End If
            Dim myCommand As New MySqlCommand(sql, connection)
            myCommand.ExecuteNonQuery()
            myCommand.Dispose()
        Catch myerror As MySqlException
            log("Post Query Failed" & myerror.Message)
        End Try
        Try
            sql = "INSERT INTO wmidata (Hostname,WMI_Name,WMI_Data, last_update)VALUES('" & hostname & "','" & hostname & "|SQLUsername','null', '" & My.Computer.Clock.LocalTime & "')"
            Dim myCommand As New MySqlCommand(sql, connection)
            myCommand.ExecuteNonQuery()
            myCommand.Dispose()
        Catch myerror As MySqlException
            log("Post Query Failed" & myerror.Message)
        End Try

        Try
            connection.Close()
            log("Successfully Completed Post Query")
        Catch myerror As MySqlException
            log("DB Close Failed" & myerror.Message)
        End Try
    End Function

    Public Function log(ByVal Entry As String)
        Dim appName As String = “SMG_RMM”
        Dim eventType As EventLogEntryType = EventLogEntryType.Information
        Dim logName = “Application”
        Dim objEventLog As New EventLog()
        Try
            If Not EventLog.SourceExists(appName) Then
                EventLog.CreateEventSource(appName, logName)
            End If
            objEventLog.Source = appName
            objEventLog.WriteEntry(Entry, eventType)
            Return True
        Catch Ex As Exception
            Return False
        End Try
    End Function
End Module