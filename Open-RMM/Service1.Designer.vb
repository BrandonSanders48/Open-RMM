Imports System.ServiceProcess

<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()> _
Partial Class Service1
    Inherits System.ServiceProcess.ServiceBase

    'UserService overrides dispose to clean up the component list.
    <System.Diagnostics.DebuggerNonUserCode()> _
    Protected Overrides Sub Dispose(ByVal disposing As Boolean)
        Try
            If disposing AndAlso components IsNot Nothing Then
                components.Dispose()
            End If
        Finally
            MyBase.Dispose(disposing)
        End Try
    End Sub

    ' The main entry point for the process
    <MTAThread()> _
    <System.Diagnostics.DebuggerNonUserCode()> _
    Shared Sub Main()
        Dim ServicesToRun() As System.ServiceProcess.ServiceBase

        ' More than one NT Service may run within the same process. To add
        ' another service to this process, change the following line to
        ' create a second service object. For example,
        '
        '   ServicesToRun = New System.ServiceProcess.ServiceBase () {New Service1, New MySecondUserService}
        '
        ServicesToRun = New System.ServiceProcess.ServiceBase() {New Service1}

        System.ServiceProcess.ServiceBase.Run(ServicesToRun)
    End Sub

    'Required by the Component Designer
    Private components As System.ComponentModel.IContainer

    ' NOTE: The following procedure is required by the Component Designer
    ' It can be modified using the Component Designer.  
    ' Do not modify it using the code editor.
    <System.Diagnostics.DebuggerStepThrough()>
    Private Sub InitializeComponent()
        Me.BackgroundWorker_OptionalFeatures = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_BIOS = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_Printers = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_ComputerSystem = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_BootConfiguration = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_LocalTime = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_PnPEntity = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_LogonSession = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_NetworkLoginProfile = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_UserAccount = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_Group = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_Product = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_CodecFile = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_BaseBoard = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_Processor = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_DesktopMonitor = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_VideoController = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_Keyboard = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_PointingDevice = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_1394Controller = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_ParallelPort = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_PCMCIAController = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_IDEControllerDevice = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_SCSIController = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_SerialPort = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_USBHub = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_SoundDevice = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_NetworkAdapters = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_LogicalDisk = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_MappedLogicalDisk = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_PhysicalMemory = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_Commands = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_Ping = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_FetchActions = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_Processes = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_Services = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_OS = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_Update = New System.ComponentModel.BackgroundWorker()
        Me.BackgroundWorker_Battery = New System.ComponentModel.BackgroundWorker()
        '
        'BackgroundWorker_OptionalFeatures
        '
        '
        'BackgroundWorker_BIOS
        '
        '
        'BackgroundWorker_Printers
        '
        '
        'BackgroundWorker_ComputerSystem
        '
        '
        'BackgroundWorker_BootConfiguration
        '
        '
        'BackgroundWorker_LocalTime
        '
        '
        'BackgroundWorker_PnPEntity
        '
        '
        'BackgroundWorker_LogonSession
        '
        '
        'BackgroundWorker_NetworkLoginProfile
        '
        '
        'BackgroundWorker_UserAccount
        '
        '
        'BackgroundWorker_Group
        '
        '
        'BackgroundWorker_Product
        '
        '
        'BackgroundWorker_CodecFile
        '
        '
        'BackgroundWorker_BaseBoard
        '
        '
        'BackgroundWorker_Processor
        '
        '
        'BackgroundWorker_DesktopMonitor
        '
        '
        'BackgroundWorker_VideoController
        '
        '
        'BackgroundWorker_Keyboard
        '
        '
        'BackgroundWorker_PointingDevice
        '
        '
        'BackgroundWorker_1394Controller
        '
        '
        'BackgroundWorker_ParallelPort
        '
        '
        'BackgroundWorker_PCMCIAController
        '
        '
        'BackgroundWorker_IDEControllerDevice
        '
        '
        'BackgroundWorker_SCSIController
        '
        '
        'BackgroundWorker_SerialPort
        '
        '
        'BackgroundWorker_USBHub
        '
        '
        'BackgroundWorker_SoundDevice
        '
        '
        'BackgroundWorker_NetworkAdapters
        '
        '
        'BackgroundWorker_LogicalDisk
        '
        '
        'BackgroundWorker_MappedLogicalDisk
        '
        '
        'BackgroundWorker_PhysicalMemory
        '
        '
        'BackgroundWorker_Ping
        '
        '
        'BackgroundWorker_FetchActions
        '
        '
        'BackgroundWorker_Processes
        '
        '
        'BackgroundWorker_Services
        '
        '
        'BackgroundWorker_OS
        '
        '
        'BackgroundWorker_Battery
        '

    End Sub

    Friend WithEvents BackgroundWorker_OptionalFeatures As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_BIOS As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_Printers As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_ComputerSystem As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_BootConfiguration As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_LocalTime As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_PnPEntity As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_LogonSession As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_NetworkLoginProfile As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_UserAccount As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_Group As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_Product As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_CodecFile As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_BaseBoard As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_Processor As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_DesktopMonitor As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_VideoController As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_Keyboard As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_PointingDevice As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_1394Controller As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_ParallelPort As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_PCMCIAController As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_IDEControllerDevice As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_SCSIController As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_SerialPort As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_USBHub As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_SoundDevice As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_NetworkAdapters As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_LogicalDisk As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_MappedLogicalDisk As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_PhysicalMemory As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_Commands As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_Ping As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_FetchActions As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_Processes As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_Services As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_OS As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_Update As ComponentModel.BackgroundWorker
    Friend WithEvents BackgroundWorker_Battery As ComponentModel.BackgroundWorker
End Class
