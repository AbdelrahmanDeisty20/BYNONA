$cert = Get-ChildItem -Path Cert:\LocalMachine\Root | Where-Object { $_.Subject -like '*Avast*' } | Select-Object -First 1
if ($cert) {
    $data = [System.Convert]::ToBase64String($cert.Export([System.Security.Cryptography.X509Certificates.X509ContentType]::Cert), 'InsertLineBreaks')
    "-----BEGIN CERTIFICATE-----`r`n$data`r`n-----END CERTIFICATE-----" | Out-File -FilePath "avast_root.crt" -Encoding ascii
    Write-Host "Exported to avast_root.crt"
} else {
    Write-Host "Avast cert not found"
}
