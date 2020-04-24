Imports System.Management
Imports System.ServiceProcess
Public Class MachineInfo
    Public Shared Function getBattery() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_Battery")
    End Function

    Public Shared Function getOperatingSystem() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_OperatingSystem")
    End Function

    Public Shared Function getComputerSystem() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_ComputerSystem")
    End Function

    Public Shared Function getBootConfiguration() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_BootConfiguration")
    End Function

    Public Shared Function getLocalTime() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_LocalTime")
    End Function

    Public Shared Function getPnPEntity() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_PnPEntity")
    End Function

    Public Shared Function getLogonSession() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_LogonSession")
    End Function

    Public Shared Function getNetworkLoginProfile() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_NetworkLoginProfile")
    End Function

    Public Shared Function getUserAccount() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_UserAccount")
    End Function

    Public Shared Function getGroup() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_Group")
    End Function

    Public Shared Function getProduct() As List(Of Dictionary(Of String, String))
        ' ContextSwitchDeadlock Exception
        Return queryWMI("SELECT * FROM Win32_Product")
    End Function

    Public Shared Function getCodecFile() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_CodecFile")
    End Function

    Public Shared Function getBaseBoard() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_BaseBoard")
    End Function

    Public Shared Function getBIOS() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_BIOS")
    End Function

    Public Shared Function getProcessor() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_Processor")
    End Function

    Public Shared Function getDesktopMonitor() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_DesktopMonitor")
    End Function

    Public Shared Function getVideoController() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_VideoController")
    End Function

    Public Shared Function getKeyboard() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_Keyboard")
    End Function

    Public Shared Function getPointingDevice() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_PointingDevice")
    End Function

    Public Shared Function get1394Controller() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_1394Controller")
    End Function

    Public Shared Function getParallelPort() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_ParallelPort")
    End Function

    Public Shared Function getPCMCIAController() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_PCMCIAController")
    End Function

    Public Shared Function getIDEControllerDevice() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_IDEControllerDevice")
    End Function

    Public Shared Function getSCSIController() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_SCSIController")
    End Function

    Public Shared Function getSerialPort() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_SerialPort")
    End Function

    Public Shared Function getUSBHub() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_USBHub")
    End Function

    Public Shared Function getSoundDevice() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_SoundDevice")
    End Function
    Public Shared Function getNetworkAdapters() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_NetworkAdapterConfiguration")
    End Function
    Public Shared Function getPrinters() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_Printer")
    End Function
    Public Shared Function getLogicalDisk() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_LogicalDisk")
    End Function
    Public Shared Function getMappedLogicalDisk() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_MappedLogicalDisk")
        Return queryWMI("SELECT * FROM Win32_MappedLogicalDisk")
    End Function
    Public Shared Function getPhysicalMemory() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_PhysicalMemory")
    End Function
    Public Shared Function getOptionalFeatures() As List(Of Dictionary(Of String, String))
        Return queryWMI("SELECT * FROM Win32_OptionalFeature")
    End Function
    Public Shared Function getProcess() As Array
        Dim processes() As Process = Process.GetProcesses()
        Return processes
    End Function

    Public Shared Function getRunningServices() As List(Of RunningServiceStructure)
        ' https://msdn.microsoft.com/en-us/library/hde9d63a(v=vs.110).aspx
        Dim retList As List(Of RunningServiceStructure) = New List(Of RunningServiceStructure)
        Dim scServices() As ServiceController
        scServices = ServiceController.GetServices()
        Dim scTemp As ServiceController
        For Each scTemp In scServices

            Dim rsStruct As RunningServiceStructure = New RunningServiceStructure()
            rsStruct.Name = scTemp.ServiceName
            rsStruct.DisplayName = scTemp.DisplayName
            Dim wmiService As ManagementObject
            wmiService = New ManagementObject("Win32_Service.Name='" + scTemp.ServiceName + "'")
            wmiService.Get()
            rsStruct.Description = CType(wmiService("Description"), String)

            If scTemp.Status = ServiceControllerStatus.Running Then
                rsStruct.Name = "Running|" & scTemp.ServiceName
            Else
                rsStruct.Name = "Stopped|" & scTemp.ServiceName
            End If

            retList.Add(rsStruct)
        Next scTemp
        Return retList
    End Function


    Public Shared Function queryWMI(strQuery As String) As List(Of Dictionary(Of String, String))
        ' return obj
        Dim retList As List(Of Dictionary(Of String, String)) = New List(Of Dictionary(Of String, String))
        Dim retDic As Dictionary(Of String, String) = New Dictionary(Of String, String)
        ' scope
        Dim mscope As ManagementScope = New ManagementScope("root\cimv2")
        ' query
        Dim selQuery As SelectQuery = New SelectQuery(strQuery)
        ' enumeration options
        Dim context As ManagementNamedValueCollection = Nothing
        Dim timeout As TimeSpan = TimeSpan.MaxValue
        Dim blockSize As Integer = 1
        Dim rewindable As Boolean = True
        Dim returnImmediately As Boolean = False
        Dim useAmendedQualifiers As Boolean = True
        Dim ensureLocatable As Boolean = True
        Dim prototypeOnly As Boolean = False
        Dim directRead As Boolean = True
        Dim enumerateDeep As Boolean = True
        Dim enumOpts As EnumerationOptions = New EnumerationOptions(context, timeout, blockSize, rewindable, returnImmediately, useAmendedQualifiers, ensureLocatable, prototypeOnly, directRead, enumerateDeep)
        ' actual searcher
        Dim searcher As New ManagementObjectSearcher(mscope, selQuery, enumOpts)
        ' now fill the dictionary
        For Each mobject As ManagementObject In searcher.Get()
            For Each prop As PropertyData In mobject.Properties
                Dim name As String = prop.Name.ToString
                Dim val As String
                If prop.Value IsNot Nothing Then
                    val = prop.Value.ToString
                Else
                    ' Nulls are empty strings
                    val = ""
                End If
                retDic.Add(name, val)
            Next
            ' add to the list
            retList.Add(retDic)
            ' reset var
            retDic = New Dictionary(Of String, String)
        Next
        ' have a nice day ;^> 
        Return retList
    End Function
End Class

Public Structure RunningServiceStructure
    Private m_Name As String
    Private m_Description As String
    Private m_DisplayName As String
    Public Sub New(name As String, displayname As String, description As String)
        m_Name = name
        m_DisplayName = displayname
        m_Description = description
    End Sub
    Public Property Name As String
        Get
            Return m_Name
        End Get
        Set(value As String)
            m_Name = value
        End Set
    End Property
    Public Property DisplayName As String
        Get
            Return m_DisplayName
        End Get
        Set(value As String)
            m_DisplayName = value
        End Set
    End Property
    Public Property Description As String
        Get
            Return m_Description
        End Get
        Set(value As String)
            m_Description = value
        End Set
    End Property
End Structure
