<?php
$dir = new RecursiveDirectoryIterator('app');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
$errors = [];
$classMap = [];

foreach ($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);

    // Check namespace
    if (preg_match('/namespace\s+([^;]+);/', $content, $nm)) {
        $namespace = trim($nm[1]);
        $expectedPath = str_replace('App\\', 'app/', $namespace . '\\');
        $expectedPath = str_replace('\\', '/', $expectedPath);

        $normalizedPath = str_replace('\\', '/', $path);
        if (!str_contains($normalizedPath, $expectedPath)) {
            $errors[] = [
                'type' => 'namespace_mismatch',
                'file' => $path,
                'namespace' => $namespace,
                'expected_in_path' => $expectedPath
            ];
        }
    }

    // Check class name vs file name
    if (preg_match('/class\s+([A-Za-z0-9_]+)/', $content, $cm)) {
        $className = trim($cm[1]);
        $filename = pathinfo($path, PATHINFO_FILENAME);

        if ($className !== $filename) {
            $errors[] = [
                'type' => 'classname_mismatch',
                'file' => $path,
                'class' => $className,
                'filename' => $filename
            ];
        }

        // Check for duplicates
        if (isset($classMap[$className])) {
            $errors[] = [
                'type' => 'duplicate_class',
                'class' => $className,
                'file1' => $classMap[$className],
                'file2' => $path
            ];
        }
        $classMap[$className] = $path;
    }
}

echo json_encode($errors, JSON_PRETTY_PRINT);
