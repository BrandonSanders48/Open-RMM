Option Explicit On
Option Infer On
Imports System.Threading
Imports System.ServiceProcess
Imports System.Timers
Imports System.IO
Imports MySql.Data.MySqlClient
Imports MySql.Data
Imports System.Data.SqlClient
Imports MySql.Web
Imports System.Drawing
Imports System
Imports System.Windows.Forms
Imports NetFwTypeLib
Imports Microsoft.Win32

Public Class Service1
    Dim hostname As String
    Dim version As String = "1.0.2.4"
    Dim ip_address As System.Net.IPHostEntry = System.Net.Dns.GetHostByName(System.Net.Dns.GetHostName)

    Protected Overrides Sub OnStart(ByVal args() As String)
        Dim sOutput As String
        Dim string_after As String
        Dim string_after2 As String

        Try
            Dim oProcess As New Process()
            Dim oStartInfo As New ProcessStartInfo("reg", "query HKEY_LOCAL_MACHINE\SOFTWARE\Wow6432Node\TeamViewer /v ClientID")
            oStartInfo.UseShellExecute = False
            oStartInfo.RedirectStandardOutput = True
            oProcess.StartInfo = oStartInfo
            oProcess.Start()
            Using oStreamReader As System.IO.StreamReader = oProcess.StandardOutput
                sOutput = oStreamReader.ReadToEnd()
            End Using
            Dim cut_at As String = "REG_DWORD"
            Dim x As Integer = InStr(sOutput, cut_at)
            Dim string_before As String = sOutput.Substring(0, x - 2)
            string_after = sOutput.Substring(x + cut_at.Length - 1)
            string_after2 = CInt("&H" & string_after.Trim)
            If (sOutput = "") Then
                log("Teamviewer Not Installed")
            End If
            'log("Teamviewer ID: " & string_after2)
        Catch ex As Exception
            log("Teamviewer Not Installed")
        End Try
        hostname = My.Computer.Name
        Dim company As String = ""
        log("SMG_RMM Started. Version: " & version)
        POST(hostname, "ServiceStart", "")
        Try
            If File.Exists("C:\SMG_RMM\company.txt") Then
                company = File.ReadAllText("C:\SMG_RMM\company.txt")
                log("Company: " & company)
                My.Computer.FileSystem.DeleteFile("C:\SMG_RMM\company.txt")
            End If
        Catch ex As Exception
            log("No Company File: " & ex.Message)
        End Try
        Try
            Dim connection As New MySqlConnection("server=" & db_server & ";" & "user=" & db_username & ";" & "password=" & db_password & ";" & "database=" & db_database & ";Connection Timeout=" & db_timeout)
            Dim sql As String
            connection.CreateCommand.CommandTimeout = 600000
            connection.Open()
            If File.Exists("C:\SMG_RMM\company.txt") Then
                sql = "UPDATE computerdata SET teamviewer='" & Trim(string_after2) & "', CompanyID='" & company & "', WHERE hostname='" & hostname & "'"
            Else
                sql = "UPDATE computerdata SET teamviewer='" & Trim(string_after2) & "' WHERE hostname='" & hostname & "'"
            End If
            POST(hostname, "AgentVersion", "{""Value"":""" & version & """}")
            Dim myCommand3 As New MySqlCommand(sql, connection)
            myCommand3.ExecuteNonQuery()
            myCommand3.Dispose()
            connection.Close()
        Catch ex As Exception
            log("Could Not Update Version: " & ex.Message)
        End Try

        BackgroundWorker_Ping.RunWorkerAsync()
        BackgroundWorker_OS.RunWorkerAsync()
        BackgroundWorker_Printers.RunWorkerAsync()
        BackgroundWorker_BIOS.RunWorkerAsync()
        BackgroundWorker_ComputerSystem.RunWorkerAsync()
        Thread.Sleep((1000 * 10)) '10 seconds
        BackgroundWorker_BootConfiguration.RunWorkerAsync()
        BackgroundWorker_PnPEntity.RunWorkerAsync()
        BackgroundWorker_NetworkLoginProfile.RunWorkerAsync()
        BackgroundWorker_UserAccount.RunWorkerAsync()
        BackgroundWorker_Group.RunWorkerAsync()
        Thread.Sleep((1000 * 10)) '10 seconds
        BackgroundWorker_Product.RunWorkerAsync()
        BackgroundWorker_BaseBoard.RunWorkerAsync()
        BackgroundWorker_Processor.RunWorkerAsync()
        BackgroundWorker_DesktopMonitor.RunWorkerAsync()
        BackgroundWorker_VideoController.RunWorkerAsync()
        BackgroundWorker_Keyboard.RunWorkerAsync()
        Thread.Sleep((1000 * 10)) '10 seconds
        BackgroundWorker_PointingDevice.RunWorkerAsync()
        BackgroundWorker_ParallelPort.RunWorkerAsync()
        BackgroundWorker_IDEControllerDevice.RunWorkerAsync()
        BackgroundWorker_SCSIController.RunWorkerAsync()
        BackgroundWorker_SerialPort.RunWorkerAsync()
        Thread.Sleep((1000 * 10)) '10 seconds
        BackgroundWorker_USBHub.RunWorkerAsync()
        BackgroundWorker_SoundDevice.RunWorkerAsync()
        BackgroundWorker_NetworkAdapters.RunWorkerAsync()
        BackgroundWorker_LogicalDisk.RunWorkerAsync()
        Thread.Sleep((1000 * 10)) '10 seconds
        BackgroundWorker_PhysicalMemory.RunWorkerAsync()
        BackgroundWorker_OptionalFeatures.RunWorkerAsync()
        BackgroundWorker_Processes.RunWorkerAsync()
        BackgroundWorker_Services.RunWorkerAsync()
        BackgroundWorker_Battery.RunWorkerAsync()
        BackgroundWorker_FetchActions.RunWorkerAsync()

        'Deprecated 4/14/20 - Uneeded. -Brad:
        'BackgroundWorker_LocalTime.RunWorkerAsync()
        'BackgroundWorker_LogonSession.RunWorkerAsync()
        'BackgroundWorker_CodecFile.RunWorkerAsync()
        'BackgroundWorker_1394Controller.RunWorkerAsync()
        'BackgroundWorker_PCMCIAController.RunWorkerAsync()
        'BackgroundWorker_MappedLogicalDisk.RunWorkerAsync()
    End Sub

    Public Function FireWallIsEnabled()
        Dim enabled As Boolean
        Dim status As String
        Try
            Dim fwMgr As Object = CreateObject("HNetCfg.FwMgr")
            Dim profile As Object = fwMgr.LocalPolicy.CurrentProfile
            Dim fwe As Integer = profile.FirewallEnabled
            enabled = (fwe <> 0)
            If enabled = True Then
                status = "Enabled"
            Else
                status = "Disabled"
            End If
        Catch ex As Exception
            status = "Disabled"
        End Try
        Return status
    End Function

    Public Function GetAssociatedProgram(ByVal FileExtension As String) As String
        Dim objExtReg As Microsoft.Win32.RegistryKey = Microsoft.Win32.Registry.ClassesRoot
        Dim objAppReg As Microsoft.Win32.RegistryKey = Microsoft.Win32.Registry.ClassesRoot
        Dim strExtValue As String
        Try
            ' Add trailing period if doesn't exist
            If FileExtension.Substring(0, 1) <> "." Then _
                FileExtension = "." & FileExtension
            ' Open registry areas containing launching app details
            objExtReg = objExtReg.OpenSubKey(FileExtension.Trim)
            strExtValue = CStr(objExtReg.GetValue(""))
            objAppReg = objAppReg.OpenSubKey(strExtValue & "\shell\open\command")
            ' Parse out, tidy up and return result
            Dim SplitArray() As String
            SplitArray = Split(CStr(objAppReg.GetValue(Nothing)), """")
            If SplitArray(0).Trim.Length > 0 Then
                Return SplitArray(0).Replace("%1", "")
            Else
                Return SplitArray(1).Replace("%1", "")
            End If
        Catch
            Return ""
        End Try
    End Function

    Protected Overrides Sub OnStop()
        log("SMG_RMM Stopped")
    End Sub

    Private Sub BackgroundWorkerPing_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_Ping.DoWork
        While True
            Dim uTime As Integer
            uTime = (DateTime.UtcNow - New DateTime(1970, 1, 1, 0, 0, 0)).TotalSeconds
            POST(hostname, "Ping", My.Computer.Clock.LocalTime)
            Thread.Sleep((1000 * 60) * 1)
        End While
    End Sub

    Dim retText_Printers_old As String
    Dim nullLoopCount_Printers As Integer = 0
    Private Sub BackgroundWorkerPrinters_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_Printers.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getPrinters)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_Printers <> retText_Printers_old Or nullLoopCount_Printers >= 10 Then
                POST(hostname, "WMI_Printers", retText_Printers)
                retText_Printers_old = retText_Printers
                nullLoopCount_Printers = 0
            Else
                nullLoopCount_Printers = nullLoopCount_Printers + 1
            End If
            Thread.Sleep((1000 * 60) * 40)
        End While
    End Sub

    Dim retText_OS_old As String
    Dim nullLoopCount_OS As Integer = 0
    Dim retText_act_old As String
    Dim retText_act As String
    Dim nullLoopCount_act As Integer = 0
    Private Sub BackgroundWorker_OS_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_OS.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getOS)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_OS <> retText_OS_old Or nullLoopCount_OS >= 10 Then
                POST(hostname, "WMI_OS", retText_OS)
                retText_OS_old = retText_OS
                nullLoopCount_OS = 0
            Else
                nullLoopCount_OS = nullLoopCount_OS + 1
            End If

            Try
                Dim act As String
                Dim searcher As New ManagementObjectSearcher("root\CIMV2", "SELECT * FROM SoftwareLicensingProduct WHERE LicenseStatus = 1")
                Dim myCollection As ManagementObjectCollection
                Dim myObject As ManagementObject
                myCollection = searcher.Get()
                If myCollection.Count = 0 Then
                    retText_act = "Not Activated"
                    searcher.Dispose()
                Else
                    For Each myObject In myCollection
                        retText_act = "Activated"
                        searcher.Dispose()
                    Next
                End If
                searcher.Dispose()
            Catch ex As Exception
            End Try
            If retText_act <> retText_act_old Or nullLoopCount_act >= 80 Then
                POST(hostname, "WindowsActivation", "{""Value"":""" & retText_act & """}")
                retText_act_old = retText_act
                nullLoopCount_act = 0
            Else
                nullLoopCount_act = nullLoopCount_act + 1
            End If
            Thread.Sleep((1000 * 60) * 15)
        End While
    End Sub

    Dim retText_ComputerSystem_old As String
    Dim nullLoopCount_ComputerSystem As Integer = 0
    Private Sub BackgroundWorker_ComputerSystem_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_ComputerSystem.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getComputerSystem)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_ComputerSystem <> retText_ComputerSystem_old Or nullLoopCount_ComputerSystem >= 10 Then
                POST(hostname, "WMI_ComputerSystem", retText_ComputerSystem)
                retText_ComputerSystem_old = retText_ComputerSystem
                nullLoopCount_ComputerSystem = 0
            Else
                nullLoopCount_ComputerSystem = nullLoopCount_ComputerSystem + 1
            End If
            Thread.Sleep((1000 * 60) * 15)
        End While
    End Sub

    Dim retText_BootConfiguration_old As String
    Dim nullLoopCount_BootConfiguration As Integer = 0
    Private Sub BackgroundWorker_BootConfiguration_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_BootConfiguration.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getBootConfig)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_BootConfig <> retText_BootConfiguration_old Or nullLoopCount_BootConfiguration >= 10 Then
                POST(hostname, "WMI_BootConfiguration", retText_BootConfig)
                retText_BootConfiguration_old = retText_BootConfig
                nullLoopCount_BootConfiguration = 0
            Else
                nullLoopCount_BootConfiguration = nullLoopCount_BootConfiguration + 1
            End If
            Thread.Sleep((1000 * 60) * 40)
        End While
    End Sub

    Dim retText_LocalTime_old As String
    Dim nullLoopCount_LocalTime As Integer = 0
    Private Sub BackgroundWorker_LocalTime_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_LocalTime.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getLocalTime)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_LocalTime <> retText_LocalTime_old Or nullLoopCount_LocalTime >= 10 Then
                POST(hostname, "WMI_LocalTime", retText_LocalTime)
                retText_LocalTime_old = retText_LocalTime
                nullLoopCount_LocalTime = 0
            Else
                nullLoopCount_LocalTime = nullLoopCount_LocalTime + 1
            End If
            Thread.Sleep((1000 * 60) * 60)
        End While
    End Sub

    Dim retText_PnPEntity_old As String
    Dim nullLoopCount_PnPEntity As Integer = 0
    Private Sub BackgroundWorker_PnPEntity_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_PnPEntity.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getPnPEntity)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_PnPEntity <> retText_PnPEntity_old Or nullLoopCount_PnPEntity >= 10 Then
                POST(hostname, "WMI_PnPEntity", retText_PnPEntity)
                retText_PnPEntity_old = retText_PnPEntity
                nullLoopCount_PnPEntity = 0
            Else
                nullLoopCount_PnPEntity = nullLoopCount_PnPEntity + 1
            End If
            Thread.Sleep((1000 * 60) * 40)
        End While
    End Sub

    Dim retText_LogonSession_old As String
    Dim nullLoopCount_LogonSession As Integer = 0
    Private Sub BackgroundWorker_LogonSession_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_LogonSession.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getLogonSession)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_LogonSession <> retText_LogonSession_old Or nullLoopCount_LogonSession >= 10 Then
                POST(hostname, "WMI_LogonSession", retText_LogonSession)
                retText_LogonSession_old = retText_LogonSession
                nullLoopCount_LogonSession = 0
            Else
                nullLoopCount_LogonSession = nullLoopCount_LogonSession + 1
            End If
            Thread.Sleep((1000 * 60) * 60)
        End While
    End Sub

    Dim retText_NetworkLoginProfile_old As String
    Dim nullLoopCount_NetworkLoginProfile As Integer = 0
    Private Sub BackgroundWorker_NetworkLoginProfile_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_NetworkLoginProfile.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getNetworkLogin)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_NetworkLogin <> retText_NetworkLoginProfile_old Or nullLoopCount_NetworkLoginProfile >= 10 Then
                POST(hostname, "WMI_NetworkLoginProfile", retText_NetworkLogin)
                retText_NetworkLoginProfile_old = retText_NetworkLogin
                nullLoopCount_NetworkLoginProfile = 0
            Else
                nullLoopCount_NetworkLoginProfile = nullLoopCount_NetworkLoginProfile + 1
            End If
            Thread.Sleep((1000 * 60) * 40)
        End While
    End Sub

    Dim retText_UserAccount_old As String
    Dim nullLoopCount_UserAccount As Integer = 0
    Private Sub BackgroundWorker_UserAccount_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_UserAccount.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getUserAccount)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_UserAccount <> retText_UserAccount_old Or nullLoopCount_UserAccount >= 10 Then
                POST(hostname, "WMI_UserAccount", retText_UserAccount)
                retText_UserAccount_old = retText_UserAccount
                nullLoopCount_UserAccount = 0
            Else
                nullLoopCount_UserAccount = nullLoopCount_UserAccount + 1
            End If
            Thread.Sleep((1000 * 60) * 40)
        End While
    End Sub

    Dim retText_Group_old As String
    Dim nullLoopCount_Group As Integer = 0
    Private Sub BackgroundWorker_Group_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_Group.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getGroup)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_Group <> retText_Group_old Or nullLoopCount_Group >= 10 Then
                POST(hostname, "WMI_Group", retText_Group)
                retText_Group_old = retText_Group
                nullLoopCount_Group = 0
            Else
                nullLoopCount_Group = nullLoopCount_Group + 1
            End If
            Thread.Sleep((1000 * 60) * 50)
        End While
    End Sub

    Dim retText_Product_old As String
    Dim nullLoopCount_Product As Integer = 0
    Public retText_DPrograms
    Dim retText_DPrograms_old As String
    Dim nullLoopCount_DPrograms As Integer = 0
    Public retText_virus
    Dim retText_virus_old As String
    Dim nullLoopCount_virus As Integer = 0
    Private Sub BackgroundWorker_Product_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_Product.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getProduct)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_Product <> retText_Product_old Or nullLoopCount_Product >= 10 Then
                POST(hostname, "WMI_Product", retText_Product)
                retText_Product_old = retText_Product
                nullLoopCount_Product = 0
            Else
                nullLoopCount_Product = nullLoopCount_Product + 1
            End If

            Dim rk As RegistryKey = Registry.ClassesRoot
            Dim programs() As String
            Dim value As String = ""
            Dim Count As Integer = 0
            programs = rk.GetSubKeyNames
            value = ""
            For Each Item In programs
                If Item(0).ToString = "." Then
                    If GetAssociatedProgram(Item) <> "" Then
                        Count = Count + 1
                        value = value & ("""" & Count & """: {""Ext""" & ": """ & Item & """,""Program""" & ": """ & Path.GetFileName(GetAssociatedProgram(Item)) & """},")
                    End If
                End If
            Next
            value = value.Remove(value.Length - 1, 1)
            retText_DPrograms = value.Replace("\", "\\")
            If retText_DPrograms <> retText_DPrograms_old Or nullLoopCount_DPrograms >= 10 Then
                POST(hostname, "DefaultPrograms", "{" & retText_DPrograms & "}")
                retText_DPrograms_old = retText_DPrograms
                nullLoopCount_DPrograms = 0
            Else
                nullLoopCount_DPrograms = nullLoopCount_DPrograms + 1
            End If
            Try
                Dim data As String = String.Empty
                For Each firewall As ManagementObject In New ManagementObjectSearcher("root\SecurityCenter" & IIf(My.Computer.Info.OSFullName.Contains("XP"), "", "2").ToString, "SELECT * FROM AntiVirusProduct").Get
                    data &= firewall("displayName").ToString & ", "
                Next
                If Not data = String.Empty Then
                    retText_virus = data
                Else
                    retText_virus = "No Antivirus,"
                End If
            Catch
                retText_virus = "No Antivirus,"
            End Try
            If retText_virus <> retText_virus_old Or nullLoopCount_virus >= 80 Then
                retText_virus = retText_virus.trim
                retText_virus = retText_virus.Remove(retText_virus.Length - 1, 1)
                POST(hostname, "Antivirus", "{""Value"":""" & retText_virus & """}")
                retText_virus_old = retText_virus
                nullLoopCount_virus = 0
            Else
                nullLoopCount_virus = nullLoopCount_virus + 1
            End If
            Thread.Sleep((1000 * 60) * 30)
        End While
    End Sub

    Dim retText_CodecFile_old As String
    Dim nullLoopCount_CodecFile As Integer = 0
    Private Sub BackgroundWorker_CodecFile_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_CodecFile.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getCodecFile)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_CodecFile <> retText_CodecFile_old Or nullLoopCount_CodecFile >= 10 Then
                POST(hostname, "WMI_CodecFile", retText_CodecFile)
                retText_CodecFile_old = retText_CodecFile
                nullLoopCount_CodecFile = 0
            Else
                nullLoopCount_CodecFile = nullLoopCount_CodecFile + 1
            End If
            Thread.Sleep((1000 * 60) * 60)
        End While
    End Sub

    Dim retText_BaseBoard_old As String
    Dim nullLoopCount_BaseBoard As Integer = 0
    Private Sub BackgroundWorker_BaseBoard_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_BaseBoard.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getBaseBoard)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_BaseBoard <> retText_BaseBoard_old Or nullLoopCount_BaseBoard >= 10 Then
                POST(hostname, "WMI_BaseBoard", retText_BaseBoard)
                retText_BaseBoard_old = retText_BaseBoard
                nullLoopCount_BaseBoard = 0
            Else
                nullLoopCount_BaseBoard = nullLoopCount_BaseBoard + 1
            End If
            Thread.Sleep((1000 * 60) * 60)
        End While
    End Sub

    Dim retText_BIOS_old As String
    Dim nullLoopCount_BIOS As Integer = 0
    Private Sub BackgroundWorkerBIOS_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_BIOS.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getBIOS)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_BIOS <> retText_BIOS_old Or nullLoopCount_BIOS >= 10 Then
                POST(hostname, "WMI_BIOS", retText_BIOS)
                retText_BIOS_old = retText_BIOS
                nullLoopCount_BIOS = 0
            Else
                nullLoopCount_BIOS = nullLoopCount_BIOS + 1
            End If
            Thread.Sleep((1000 * 60) * 30)
        End While
    End Sub

    Dim retText_Processor_old As String
    Dim nullLoopCount_Processor As Integer = 0
    Dim retText_usage_old As String
    Public retText_usage As String
    Dim nullLoopCount_usage As Integer = 0
    Private Sub BackgroundWorker_Processor_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_Processor.DoWork
        While True
            Dim infoThread As Thread
            Dim i As String
            infoThread = New Thread(AddressOf getProcessor)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_Processor <> retText_Processor_old Or nullLoopCount_Processor >= 10 Then
                POST(hostname, "WMI_Processor", retText_Processor)
                retText_Processor_old = retText_Processor
                nullLoopCount_Processor = 0
            Else
                nullLoopCount_Processor = nullLoopCount_Processor + 1
            End If
            Dim cpu As New PerformanceCounter()
            With cpu
                .CategoryName = "Processor"
                .CounterName = "% Processor Time"
                .InstanceName = "_Total"
            End With
            System.Threading.Thread.Sleep(1000)
            i = cpu.NextValue
            System.Threading.Thread.Sleep(1000)
            retText_usage = cpu.NextValue
            If retText_usage <> retText_usage_old Or nullLoopCount_usage >= 10 Then
                POST(hostname, "CPUUsage", "{""Value"":""" & retText_usage & """}")
                retText_usage_old = retText_usage
                nullLoopCount_usage = 0
            Else
                nullLoopCount_usage = nullLoopCount_usage + 1
            End If
            Thread.Sleep((1000 * 60) * 15)
        End While
    End Sub

    Dim retText_DesktopMonitor_old As String
    Dim nullLoopCount_DesktopMonitor As Integer = 0
    Private Sub BackgroundWorker_DesktopMonitor_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_DesktopMonitor.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getDesktopMonitor)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_DesktopMonitor <> retText_DesktopMonitor_old Or nullLoopCount_DesktopMonitor >= 10 Then
                POST(hostname, "WMI_DesktopMonitor", retText_DesktopMonitor)
                retText_DesktopMonitor_old = retText_DesktopMonitor
                nullLoopCount_DesktopMonitor = 0
            Else
                nullLoopCount_DesktopMonitor = nullLoopCount_DesktopMonitor + 1
            End If
            Thread.Sleep((1000 * 60) * 30)
        End While
    End Sub

    Dim retText_VideoController_old As String
    Dim nullLoopCount_VideoController As Integer = 0
    Private Sub BackgroundWorker_VideoController_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_VideoController.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getVideoController)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_VideoController <> retText_VideoController_old Or nullLoopCount_VideoController >= 10 Then
                POST(hostname, "WMI_VideoController", retText_VideoController)
                retText_VideoController_old = retText_VideoController
                nullLoopCount_VideoController = 0
            Else
                nullLoopCount_VideoController = nullLoopCount_VideoController + 1
            End If
            Thread.Sleep((1000 * 60) * 30)
        End While
    End Sub

    Dim retText_Keyboard_old As String
    Dim nullLoopCount_Keyboard As Integer = 0
    Private Sub BackgroundWorker_Keyboard_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_Keyboard.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getKeyboard)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_Keyboard <> retText_Keyboard_old Or nullLoopCount_Keyboard >= 10 Then
                POST(hostname, "WMI_Keyboard", retText_Keyboard)
                retText_Keyboard_old = retText_Keyboard
                nullLoopCount_Keyboard = 0
            Else
                nullLoopCount_Keyboard = nullLoopCount_Keyboard + 1
            End If
            Thread.Sleep((1000 * 60) * 30)
        End While
    End Sub

    Dim retText_PointingDevice_old As String
    Dim nullLoopCount_PointingDevice As Integer = 0
    Private Sub BackgroundWorker_PointingDevice_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_PointingDevice.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getPointingDevice)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_PointingDevice <> retText_PointingDevice_old Or nullLoopCount_PointingDevice >= 10 Then
                POST(hostname, "WMI_PointingDevice", retText_PointingDevice)
                retText_PointingDevice_old = retText_PointingDevice
                nullLoopCount_PointingDevice = 0
            Else
                nullLoopCount_PointingDevice = nullLoopCount_PointingDevice + 1
            End If
            Thread.Sleep((1000 * 60) * 30)
        End While
    End Sub

    Dim retText_1394Controller_old As String
    Dim nullLoopCount_1394Controller As Integer = 0
    Private Sub BackgroundWorker_1394Controller_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_1394Controller.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf get1394Controller)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_1394Controller <> retText_1394Controller_old Or nullLoopCount_1394Controller >= 10 Then
                POST(hostname, "WMI_1394Controller", retText_1394Controller)
                retText_1394Controller_old = retText_1394Controller
                nullLoopCount_1394Controller = 0
            Else
                nullLoopCount_1394Controller = nullLoopCount_1394Controller + 1
            End If
            Thread.Sleep((1000 * 60) * 60)
        End While
    End Sub

    Dim retText_ParallelPort_old As String
    Dim nullLoopCount_ParallelPort As Integer = 0
    Private Sub BackgroundWorker_ParallelPort_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_ParallelPort.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getParallelPort)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_ParallelPort <> retText_ParallelPort_old Or nullLoopCount_ParallelPort >= 10 Then
                POST(hostname, "WMI_ParallelPort", retText_ParallelPort)
                retText_ParallelPort_old = retText_ParallelPort
                nullLoopCount_ParallelPort = 0
            Else
                nullLoopCount_ParallelPort = nullLoopCount_ParallelPort + 1
            End If
            Thread.Sleep((1000 * 60) * 60)
        End While
    End Sub

    Dim retText_PCMCIAController_old As String
    Dim nullLoopCount_PCMCIAController As Integer = 0
    Private Sub BackgroundWorker_PCMCIAController_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_PCMCIAController.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getPCMCIAController)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_PCMCIAController <> retText_PCMCIAController_old Or nullLoopCount_PCMCIAController >= 10 Then
                POST(hostname, "WMI_PCMCIAController", retText_PCMCIAController)
                retText_PCMCIAController_old = retText_PCMCIAController
                nullLoopCount_PCMCIAController = 0
            Else
                nullLoopCount_PCMCIAController = nullLoopCount_PCMCIAController + 1
            End If
            Thread.Sleep((1000 * 60) * 60)
        End While
    End Sub

    Dim retText_IDEControllerDevice_old As String
    Dim nullLoopCount_IDEControllerDevice As Integer = 0
    Private Sub BackgroundWorker_IDEControllerDevice_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_IDEControllerDevice.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getIDEControllerDevice)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_IDEControllerDevice <> retText_IDEControllerDevice_old Or nullLoopCount_IDEControllerDevice >= 10 Then
                POST(hostname, "WMI_IDEControllerDevice", retText_IDEControllerDevice)
                retText_IDEControllerDevice_old = retText_IDEControllerDevice
                nullLoopCount_IDEControllerDevice = 0
            Else
                nullLoopCount_IDEControllerDevice = nullLoopCount_IDEControllerDevice + 1
            End If
            Thread.Sleep((1000 * 60) * 60)
        End While
    End Sub

    Dim retText_SCSIController_old As String
    Dim nullLoopCount_SCSIController As Integer = 0
    Private Sub BackgroundWorker_SCSIController_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_SCSIController.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getSCSIController)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_SCSIController <> retText_SCSIController_old Or nullLoopCount_SCSIController >= 10 Then
                POST(hostname, "WMI_SCSIController", retText_SCSIController)
                retText_SCSIController_old = retText_SCSIController
                nullLoopCount_SCSIController = 0
            Else
                nullLoopCount_SCSIController = nullLoopCount_SCSIController + 1
            End If
            Thread.Sleep((1000 * 60) * 60)
        End While
    End Sub

    Dim retText_SerialPort_old As String
    Dim nullLoopCount_SerialPort As Integer = 0
    Private Sub BackgroundWorker_SerialPort_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_SerialPort.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getSerialPort)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_SerialPort <> retText_SerialPort_old Or nullLoopCount_SerialPort >= 10 Then
                POST(hostname, "WMI_SerialPort", retText_SerialPort)
                retText_SerialPort_old = retText_SerialPort
                nullLoopCount_SerialPort = 0
            Else
                nullLoopCount_SerialPort = nullLoopCount_SerialPort + 1
            End If
            Thread.Sleep((1000 * 60) * 30)
        End While
    End Sub

    Dim retText_USBHub_old As String
    Dim nullLoopCount_USBHub As Integer = 0
    Private Sub BackgroundWorker_USBHub_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_USBHub.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getUSBHub)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_USBHub <> retText_USBHub_old Or nullLoopCount_USBHub >= 10 Then
                POST(hostname, "WMI_USBHub", retText_USBHub)
                retText_USBHub_old = retText_USBHub
                nullLoopCount_USBHub = 0
            Else
                nullLoopCount_USBHub = nullLoopCount_USBHub + 1
            End If
            Thread.Sleep((1000 * 60) * 30)
        End While
    End Sub

    Dim retText_SoundDevice_old As String
    Dim nullLoopCount_SoundDevice As Integer = 0
    Private Sub BackgroundWorker_SoundDevice_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_SoundDevice.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getSoundDevice)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_SoundDevice <> retText_SoundDevice_old Or nullLoopCount_SoundDevice >= 10 Then
                POST(hostname, "WMI_SoundDevice", retText_SoundDevice)
                retText_SoundDevice_old = retText_SoundDevice
                nullLoopCount_SoundDevice = 0
            Else
                nullLoopCount_SoundDevice = nullLoopCount_SoundDevice + 1
            End If
            Thread.Sleep((1000 * 60) * 30)
        End While
    End Sub

    Dim retText_NetworkAdapters_old As String
    Dim nullLoopCount_NetworkAdapters As Integer = 0
    Dim retText_Firewall_old As String
    Dim nullLoopCount_Firewall As Integer = 0
    Public retText_Firewall As String
    Dim retText_ip_old As String
    Dim nullLoopCount_ip As Integer = 0
    Public retText_ip As String
    Private Sub BackgroundWorker_NetworkAdapters_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_NetworkAdapters.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getNetworkAdapters)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_NetworkAdapters <> retText_NetworkAdapters_old Or nullLoopCount_NetworkAdapters >= 10 Then
                POST(hostname, "WMI_NetworkAdapters", retText_NetworkAdapters)
                retText_NetworkAdapters_old = retText_NetworkAdapters
                nullLoopCount_NetworkAdapters = 0
            Else
                nullLoopCount_NetworkAdapters = nullLoopCount_NetworkAdapters + 1
            End If
            retText_Firewall = FireWallIsEnabled()
            If retText_Firewall <> retText_Firewall_old Or nullLoopCount_Firewall >= 10 Then
                POST(hostname, "Firewall", "{""Status"":""" & retText_Firewall & """}")
                retText_Firewall_old = retText_Firewall
                nullLoopCount_Firewall = 0
            Else
                nullLoopCount_Firewall = nullLoopCount_Firewall + 1
            End If
            retText_ip = ip_address.AddressList.GetValue(0).ToString
            If retText_ip <> retText_ip_old Or nullLoopCount_ip >= 10 Then
                POST(hostname, "IPAddress", "{""Value"":""" & retText_ip & """}")
                retText_ip_old = retText_ip
                nullLoopCount_ip = 0
            Else
                nullLoopCount_ip = nullLoopCount_ip + 1
            End If
            Thread.Sleep((1000 * 60) * 20)
        End While
    End Sub

    Dim retText_LogicalDisk_old As String
    Dim nullLoopCount_LogicalDisk As Integer = 0
    Private Sub BackgroundWorker_LogicalDisk_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_LogicalDisk.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getLogicalDisk)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_LogicalDisk <> retText_LogicalDisk_old Or nullLoopCount_LogicalDisk >= 10 Then
                POST(hostname, "WMI_LogicalDisk", retText_LogicalDisk)
                retText_LogicalDisk_old = retText_LogicalDisk
                nullLoopCount_LogicalDisk = 0
            Else
                nullLoopCount_LogicalDisk = nullLoopCount_LogicalDisk + 1
            End If
            Thread.Sleep((1000 * 60) * 20)
        End While
    End Sub

    Dim retText_MappedLogicalDisk_old As String
    Dim nullLoopCount_MappedLogicalDisk As Integer = 0
    Private Sub BackgroundWorker_MappedLogicalDisk_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_MappedLogicalDisk.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getMappedLogicalDisk)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_MappedLogicalDisk <> retText_MappedLogicalDisk_old Or nullLoopCount_MappedLogicalDisk >= 10 Then
                POST(hostname, "WMI_MappedLogicalDisk", retText_MappedLogicalDisk)
                retText_MappedLogicalDisk_old = retText_MappedLogicalDisk
                nullLoopCount_MappedLogicalDisk = 0
            Else
                nullLoopCount_MappedLogicalDisk = nullLoopCount_MappedLogicalDisk + 1
            End If
            Thread.Sleep((1000 * 60) * 30)
        End While
    End Sub

    Dim retText_PhysicalMemory_old As String
    Dim nullLoopCount_PhysicalMemory As Integer = 0
    Private Sub BackgroundWorker_PhysicalMemory_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_PhysicalMemory.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getPhysicalMemory)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_PhysicalMemory <> retText_PhysicalMemory_old Or nullLoopCount_PhysicalMemory >= 10 Then
                POST(hostname, "WMI_PhysicalMemory", retText_PhysicalMemory)
                retText_PhysicalMemory_old = retText_PhysicalMemory
                nullLoopCount_PhysicalMemory = 0
            Else
                nullLoopCount_PhysicalMemory = nullLoopCount_PhysicalMemory + 1
            End If
            Thread.Sleep((1000 * 60) * 15)
        End While
    End Sub

    Dim retText_OptionalFeatures_old As String
    Dim nullLoopCount_OptionalFeatures As Integer = 0
    Private Sub BackgroundWorker_OptionalFeatures_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_OptionalFeatures.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getOptionalFeatures)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_OptionalFeatures <> retText_OptionalFeatures_old Or nullLoopCount_OptionalFeatures >= 10 Then
                POST(hostname, "WMI_OptionalFeatures", retText_OptionalFeatures)
                retText_OptionalFeatures_old = retText_OptionalFeatures
                nullLoopCount_OptionalFeatures = 0
            Else
                nullLoopCount_OptionalFeatures = nullLoopCount_OptionalFeatures + 1
            End If
            Thread.Sleep((1000 * 60) * 60)
        End While
    End Sub

    Dim retText_Battery_old As String

    Private Sub BackgroundWorker_Battery_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_Battery.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getBattery)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_Battery <> retText_Battery_old Then
                POST(hostname, "WMI_Battery", retText_Battery)
                retText_Battery_old = retText_Battery
            End If
            Thread.Sleep((1000 * 60) * 2)
        End While
    End Sub

    Dim retText_Processes_old As String
    Dim nullLoopCount_Processes As Integer = 0
    Private Sub BackgroundWorker_Proccesses_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_Processes.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getProccess)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_Proccesses <> retText_Processes_old Or nullLoopCount_Processes >= 10 Then
                POST(hostname, "WMI_Processes", retText_Proccesses)
                retText_Processes_old = retText_Proccesses
                nullLoopCount_Processes = 0
            Else
                nullLoopCount_Processes = nullLoopCount_Processes + 1
            End If
            Thread.Sleep((1000 * 60) * 20)
        End While
    End Sub

    Dim retText_Services_old As String
    Dim nullLoopCount_Services As Integer = 0
    Private Sub BackgroundWorker_Services_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_Services.DoWork
        While True
            Dim infoThread As Thread
            infoThread = New Thread(AddressOf getServices)
            infoThread.Start()
            Do Until infoThread.ThreadState = ThreadState.Stopped
            Loop
            If retText_Services <> retText_Services_old Or nullLoopCount_Services >= 10 Then
                POST(hostname, "WMI_Services", retText_Services)
                retText_Services_old = retText_Services
                nullLoopCount_Services = 0
            Else
                nullLoopCount_Services = nullLoopCount_Services + 1
            End If
            Thread.Sleep((1000 * 60) * 20)
        End While
    End Sub

    Public Sub New()
        ' This call is required by the designer.
        InitializeComponent()
        ' Add any initialization after the InitializeComponent() call.
    End Sub
    Private Sub BackgroundWorker_FetchActions_DoWork(sender As Object, e As System.ComponentModel.DoWorkEventArgs) Handles BackgroundWorker_FetchActions.DoWork
        'get shell commands
        While True
            Try
                Dim uTime As Integer
                uTime = (DateTime.UtcNow - New DateTime(1970, 1, 1, 0, 0, 0)).TotalSeconds
                Dim connection As New MySqlConnection("server=" & db_server & ";" & "user=" & db_username & ";" & "password=" & db_password & ";" & "database=" & db_database & ";Connection Timeout=" & db_timeout)
                Dim sql As String
                connection.CreateCommand.CommandTimeout = 600000
                connection.Open()
                Dim command As String
                Dim argument As String
                Dim expire As String
                Dim id As String
                Dim myAdapter As New MySqlDataAdapter
                sql = "SELECT * FROM commands WHERE ComputerID='" & hostname & "' and status='Sent' Limit 1"
                Dim myCommand2 As New MySqlCommand(sql, connection)
                myAdapter.SelectCommand = myCommand2
                Dim myData As MySqlDataReader
                myData = myCommand2.ExecuteReader()
                If myData.HasRows Then
                    While myData.Read()
                        command = myData("command").ToString
                        argument = myData("arg").ToString
                        expire = myData("expire_time").ToString
                        id = myData("ID").ToString
                    End While
                    myCommand2.Dispose()
                    Dim date1 As DateTime = CDate(expire)
                    Dim date2 As DateTime = DateTime.Now
                    Dim sOutput As String
                    'log("command " & command)
                    'log("arg " & argument)
                    If date2 < date1 Then
                        If command = "kill" Then
                            Dim aProcess As System.Diagnostics.Process
                            aProcess = System.Diagnostics.Process.GetProcessById(argument)
                            aProcess.Kill()
                        Else
                            Dim oProcess As New Process()
                            Dim oStartInfo As New ProcessStartInfo(command, argument)
                            oStartInfo.UseShellExecute = False
                            oStartInfo.CreateNoWindow = True
                            oStartInfo.RedirectStandardOutput = True
                            oProcess.StartInfo = oStartInfo
                            oProcess.Start()
                            'times out and stalls
                            '  Using oStreamReader As System.IO.StreamReader = oProcess.StandardOutput
                            ' sOutput = oProcess.StandardOutput.ReadToEnd.ToString
                            'oProcess.Close()
                            'End Using
                        End If
                        If (sOutput = "") Then
                            sOutput = "Command Recieved"
                        End If
                        sql = "UPDATE commands SET time_received='" & uTime & "', data_received='" & sOutput & "', status='Received' WHERE ID='" & id & "'"
                        Dim myCommand3 As New MySqlCommand(sql, connection)
                        myCommand3.ExecuteNonQuery()
                        myCommand3.Dispose()
                    End If
                End If
                myData.Close()
                connection.Close()
                log("Successfully Completed Fetch Actions Query")
            Catch ex As Exception
                log("Fetch Actions Query Failed:" & ex.Message)
            End Try
            Thread.Sleep((1000 * 60) * 1) '1min
        End While
    End Sub
End Class