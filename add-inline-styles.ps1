# PowerShell script to add inline style to all <th> elements in Blade files

$files = Get-ChildItem -Path "resources\views" -Filter "*.blade.php" -Recurse

foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    
    # Skip if no <th> tags
    if ($content -notmatch '<th') {
        continue
    }
    
    # Replace <th> with <th style="text-align:center">
    # Handle <th>, <th class="...">, <th class="..." ...>
    $content = $content -replace '<th\s+', '<th style="text-align:center" '
    $content = $content -replace '<th>', '<th style="text-align:center">'
    
    # Handle <th style="..."> - merge styles
    $content = $content -replace '<th style="text-align:center" style="([^"]*)">', '<th style="text-align:center; $1">'
    
    Set-Content $file.FullName -Value $content -NoNewline
    Write-Host "Updated: $($file.FullName)"
}

Write-Host "`nDone! All <th> elements now have inline style."
