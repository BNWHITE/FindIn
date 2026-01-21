<?php
// Simple upload utilities
function ensure_dir(string $path) {
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

function safe_filename(string $name) : string {
    // keep extension
    $name = preg_replace('/[^A-Za-z0-9_\.-]/', '_', $name);
    return $name;
}

function move_uploaded_file_safe(array $file, string $destDir, array $allowed_exts = [], int $max_bytes = 5242880, array $allowed_mime_patterns = []) : ?string {
    // max_bytes default 5MB
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) return null;
    if (!empty($file['error'])) return null;

    // size check
    if (isset($file['size']) && $file['size'] > 0 && $file['size'] > $max_bytes) {
        return null;
    }

    // extension check
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!empty($allowed_exts) && !in_array($ext, $allowed_exts)) {
        return null;
    }

    // mime check (optional)
    if (!empty($allowed_mime_patterns) && function_exists('finfo_open')) {
        $f = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($f, $file['tmp_name']);
        finfo_close($f);
        $ok = false;
        foreach ($allowed_mime_patterns as $pat) {
            if (preg_match('#' . str_replace('/', '\/', $pat) . '#i', $mime)) { $ok = true; break; }
        }
        if (!$ok) return null;
    }

    ensure_dir($destDir);
    $original = basename($file['name']);
    $safe = time() . '_' . safe_filename($original);
    $target = rtrim($destDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $safe;
    if (move_uploaded_file($file['tmp_name'], $target)) {
        return $target;
    }
    return null;
}

function download_link_for_path(string $path) : string {
    // Convert an absolute path within project to a web path if possible
    $cwd = realpath(__DIR__ . '/../');
    $real = realpath($path);
    if ($real !== false && strpos($real, $cwd) === 0) {
        $rel = substr($real, strlen($cwd));
        return str_replace(DIRECTORY_SEPARATOR, '/', $rel);
    }
    return $path;
}
