# ROBUST PowerShell script to add inline style to ALL th elements in Blade files
# Handles all variations: th, TH, th class, multiline, existing styles, etc.

$files = Get-ChildItem -Path "resources\views" -Filter "*.blade.php" -Recurse
$totalUpdated = 0

foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw -Encoding UTF8
    $originalContent = $content
    
    # Skip if no th tags (case-insensitive)
    if ($content -notmatch '(?i)<th[\s>]') {
        continue
    }
    
    # Regex pattern to match ALL th variations (case-insensitive, multiline, with/without attributes)
    $pattern = '(?i)<th(?<attrs>(?:\s+[^>]*?)?)>'
    
    $content = [regex]::Replace($content, $pattern, {
        param($match)
        
        $attrs = $match.Groups['attrs'].Value
        
        # Check if style attribute already exists
        if ($attrs -match '(?i)\s+style\s*=\s*"([^"]*)"') {
            $existingStyle = $matches[1]
            
            # Check if text-align:center already exists
            if ($existingStyle -match '(?i)text-align\s*:\s*center') {
                # Already has text-align:center, return as-is
                return $match.Value
            } else {
                # Append text-align:center to existing style
                $newStyle = $existingStyle.TrimEnd(';') + '; text-align:center'
                $newAttrs = $attrs -replace '(?i)(\s+style\s*=\s*")([^"]*)"', "`$1$newStyle`""
                return "<th$newAttrs>"
            }
        } else {
            # No style attribute, add it
            return "<th style=`"text-align:center`"$attrs>"
        }
    })
    
    # Only write if content changed
    if ($content -ne $originalContent) {
        Set-Content $file.FullName -Value $content -NoNewline -Encoding UTF8
        $totalUpdated++
        Write-Host "Updated: $($file.Name)"
    }
}

Write-Host ""
Write-Host "========================================"
Write-Host "COMPLETE! Updated $totalUpdated files."
Write-Host "All th elements now have text-align:center"
Write-Host "========================================"
