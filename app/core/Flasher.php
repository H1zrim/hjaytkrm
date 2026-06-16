<?php

class Flasher {
    public static function setFlash($type, $msg) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['flash'] = [
            'type' => $type,
            'msg'  => $msg
        ];
    }

    public static function flash() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (isset($_SESSION['flash'])) {
            $f = $_SESSION['flash'];
            unset($_SESSION['flash']);

            $colors = [
                'success' => ['bg'=>'#d4edda','border'=>'#c3e6cb','text'=>'#155724','icon'=>'✅'],
                'error'   => ['bg'=>'#f8d7da','border'=>'#f5c6cb','text'=>'#721c24','icon'=>'❌'],
                'info'    => ['bg'=>'#d1ecf1','border'=>'#bee5eb','text'=>'#0c5460','icon'=>'ℹ️'],
                'warning' => ['bg'=>'#fff3cd','border'=>'#ffeeba','text'=>'#856404','icon'=>'⚠️'],
            ];

            $c = $colors[$f['type']] ?? $colors['info'];

            echo '<div style="background:'.$c['bg'].'; border:1px solid '.$c['border'].'; color:'.$c['text'].'; padding:12px 18px; border-radius:8px; margin-bottom:20px; font-size:14px; display:flex; align-items:center; gap:10px;">'
                . $c['icon'] . ' ' . htmlspecialchars($f['msg']) . '</div>';
        }
    }
}