<?php

namespace App\Services;

use App\Models\SystemSetting;

class ThemeService
{
    /**
     * Get all theme settings
     */
    public static function getThemeSettings()
    {
        return [
            'primary_bg_color' => SystemSetting::getValue('primary_bg_color', '#f8f9fa'),
            'secondary_bg_color' => SystemSetting::getValue('secondary_bg_color', '#ffffff'),
            'primary_text_color' => SystemSetting::getValue('primary_text_color', '#212529'),
            'secondary_text_color' => SystemSetting::getValue('secondary_text_color', '#6c757d'),
            'accent_color' => SystemSetting::getValue('accent_color', '#fd7e14'),
            'border_color' => SystemSetting::getValue('border_color', '#dee2e6'),
            'navbar_bg_color' => SystemSetting::getValue('navbar_bg_color', '#212529'),
            'navbar_text_color' => SystemSetting::getValue('navbar_text_color', '#ffffff'),
            'sidebar_bg_color' => SystemSetting::getValue('sidebar_bg_color', '#fd7e14'),
            'sidebar_text_color' => SystemSetting::getValue('sidebar_text_color', '#ffffff'),
            'sidebar_hover_color' => SystemSetting::getValue('sidebar_hover_color', '#e55a00'),
            'page_header_bg_color' => SystemSetting::getValue('page_header_bg_color', '#f8f9fa'),
            'brand_gradient' => SystemSetting::getValue('brand_gradient', 'linear-gradient(135deg, #fdb838 0%, #ef473e 100%)'),
            'sidebar_gradient' => SystemSetting::getValue('sidebar_gradient', 'linear-gradient(135deg, #fdb838 0%, #ef473e 100%)'),
            'navbar_gradient' => SystemSetting::getValue('navbar_gradient', 'linear-gradient(135deg, #212529 0%, #343a40 100%)'),
            'accent_gradient' => SystemSetting::getValue('accent_gradient', 'linear-gradient(135deg, #fdb838 0%, #ef473e 100%)'),
            'use_gradients' => SystemSetting::getValue('use_gradients', 'true'),
        ];
    }

    /**
     * Generate CSS for theme colors
     */
    public static function generateThemeCSS()
    {
        $settings = self::getThemeSettings();
        
        // Get cache buster for theme assets
        $cacheBuster = SystemSetting::getValue('theme_cache_buster', time());
        
        $css = "/* Theme CSS - Generated: " . date('Y-m-d H:i:s') . " - Cache Buster: {$cacheBuster} */\n";
        $css .= ":root {\n";
        $css .= "    --custom-primary-bg: {$settings['primary_bg_color']};\n";
        $css .= "    --custom-secondary-bg: {$settings['secondary_bg_color']};\n";
        $css .= "    --custom-primary-text: {$settings['primary_text_color']};\n";
        $css .= "    --custom-secondary-text: {$settings['secondary_text_color']};\n";
        $css .= "    --custom-accent: {$settings['accent_color']};\n";
        $css .= "    --custom-border: {$settings['border_color']};\n";
        $css .= "    --custom-navbar-bg: {$settings['navbar_bg_color']};\n";
        $css .= "    --custom-navbar-text: {$settings['navbar_text_color']};\n";
        $css .= "    --custom-sidebar-bg: {$settings['sidebar_bg_color']};\n";
        $css .= "    --custom-sidebar-text: {$settings['sidebar_text_color']};\n";
        $css .= "    --custom-sidebar-hover: {$settings['sidebar_hover_color']};\n";
        $css .= "    --custom-page-header-bg: {$settings['page_header_bg_color']};\n";
        $css .= "    --custom-brand-gradient: {$settings['brand_gradient']};\n";
        $css .= "    --custom-sidebar-gradient: {$settings['sidebar_gradient']};\n";
        $css .= "    --custom-navbar-gradient: {$settings['navbar_gradient']};\n";
        $css .= "    --custom-accent-gradient: {$settings['accent_gradient']};\n";
        $css .= "    --theme-cache-buster: {$cacheBuster};\n";
        $css .= "}\n\n";

        $css .= "body {\n";
        $css .= "    background-color: var(--custom-primary-bg) !important;\n";
        $css .= "    color: var(--custom-primary-text) !important;\n";
        $css .= "}\n\n";

        $css .= ".card {\n";
        $css .= "    background-color: var(--custom-secondary-bg) !important;\n";
        $css .= "    border-color: var(--custom-border) !important;\n";
        $css .= "}\n\n";

        $css .= ".navbar {\n";
        if ($settings['use_gradients'] == 1) {
            $css .= "    background: var(--custom-navbar-gradient) !important;\n";
            $css .= "    background-image: var(--custom-navbar-gradient) !important;\n";
        } else {
            $css .= "    background-color: var(--custom-navbar-bg) !important;\n";
            $css .= "    background: var(--custom-navbar-bg) !important;\n";
        }
        $css .= "}\n\n";

        $css .= ".navbar .nav-link, .navbar .navbar-brand {\n";
        $css .= "    color: var(--custom-navbar-text) !important;\n";
        $css .= "}\n\n";

        $css .= ".btn-primary {\n";
        if ($settings['use_gradients'] == 1) {
            $css .= "    background: var(--custom-accent-gradient) !important;\n";
            $css .= "    background-image: var(--custom-accent-gradient) !important;\n";
            $css .= "    border: none !important;\n";
        } else {
            $css .= "    background-color: var(--custom-accent) !important;\n";
            $css .= "    background: var(--custom-accent) !important;\n";
            $css .= "    border-color: var(--custom-accent) !important;\n";
        }
        $css .= "}\n\n";

        $css .= ".btn-primary:hover {\n";
        if ($settings['use_gradients'] == 1) {
            $css .= "    background: var(--custom-accent-gradient) !important;\n";
            $css .= "    background-image: var(--custom-accent-gradient) !important;\n";
            $css .= "    filter: brightness(0.9) !important;\n";
        } else {
            $css .= "    background-color: var(--custom-accent) !important;\n";
            $css .= "    background: var(--custom-accent) !important;\n";
            $css .= "    border-color: var(--custom-accent) !important;\n";
            $css .= "    filter: brightness(0.9) !important;\n";
        }
        $css .= "}\n\n";

        $css .= "h1, h2, h3, h4, h5, h6 {\n";
        $css .= "    color: var(--custom-primary-text) !important;\n";
        //$css .= "    background-color: var(--custom-page-header-bg) !important;\n";
        $css .= "    padding: 0.5rem 1rem;\n";
        $css .= "    border-radius: 8px;\n";
        $css .= "    margin-bottom: 1rem;\n";
        $css .= "}\n\n";

        $css .= ".text-muted {\n";
        $css .= "    color: var(--custom-secondary-text) !important;\n";
        $css .= "}\n\n";

        $css .= ".text-primary {\n";
        $css .= "    color: var(--custom-primary-text) !important;\n";
        $css .= "}\n\n";

        $css .= ".text-secondary {\n";
        $css .= "    color: var(--custom-secondary-text) !important;\n";
        $css .= "}\n\n";

        $css .= ".text-accent {\n";
        $css .= "    color: var(--custom-accent) !important;\n";
        $css .= "}\n\n";

        $css .= ".border {\n";
        $css .= "    border-color: var(--custom-border) !important;\n";
        $css .= "}\n\n";

        $css .= ".table {\n";
        $css .= "    color: var(--custom-primary-text) !important;\n";
        $css .= "}\n\n";

        $css .= ".table th {\n";
        $css .= "    background-color: var(--custom-secondary-bg) !important;\n";
        $css .= "    color: var(--custom-primary-text) !important;\n";
        $css .= "    border-color: var(--custom-border) !important;\n";
        $css .= "}\n\n";

        $css .= ".table td {\n";
        $css .= "    border-color: var(--custom-border) !important;\n";
        $css .= "}\n\n";

        $css .= ".form-control {\n";
        $css .= "    background-color: var(--custom-secondary-bg) !important;\n";
        $css .= "    color: var(--custom-primary-text) !important;\n";
        $css .= "    border-color: var(--custom-border) !important;\n";
        $css .= "}\n\n";

        $css .= ".form-control:focus {\n";
        $css .= "    background-color: var(--custom-secondary-bg) !important;\n";
        $css .= "    color: var(--custom-primary-text) !important;\n";
        $css .= "    border-color: var(--custom-accent) !important;\n";
        $css .= "    box-shadow: 0 0 0 0.2rem rgba(" . self::hexToRgb($settings['accent_color']) . ", 0.25) !important;\n";
        $css .= "}\n\n";

        $css .= ".alert {\n";
        $css .= "    background-color: var(--custom-secondary-bg) !important;\n";
        $css .= "    border-color: var(--custom-border) !important;\n";
        $css .= "    color: var(--custom-primary-text) !important;\n";
        $css .= "}\n\n";

        // Sidebar styles
        $css .= ".sidebar-brand {\n";
        if ($settings['use_gradients'] == 1) {
            $css .= "    background: var(--custom-sidebar-gradient) !important;\n";
            $css .= "    background-image: var(--custom-sidebar-gradient) !important;\n";
        } else {
            $css .= "    background-color: var(--custom-sidebar-bg) !important;\n";
            $css .= "    background: var(--custom-sidebar-bg) !important;\n";
        }
        $css .= "}\n\n";

        $css .= ".sidebar-brand .nav-link {\n";
        $css .= "    color: var(--custom-sidebar-text) !important;\n";
        $css .= "}\n\n";

        $css .= ".sidebar-brand .nav-link:hover {\n";
        $css .= "    background-color: var(--custom-sidebar-hover) !important;\n";
        $css .= "    color: var(--custom-sidebar-text) !important;\n";
        $css .= "}\n\n";

        $css .= ".sidebar-brand .navbar-brand {\n";
        $css .= "    color: var(--custom-sidebar-text) !important;\n";
        $css .= "}\n\n";

        $css .= ".sidebar-brand .text-muted {\n";
        $css .= "    color: var(--custom-sidebar-text) !important;\n";
        $css .= "    opacity: 0.8;\n";
        $css .= "}\n\n";

        // Brand gradient styles
        $css .= ".btn-brand {\n";
        if ($settings['use_gradients'] == 1) {
            $css .= "    background: var(--custom-brand-gradient) !important;\n";
            $css .= "    background-image: var(--custom-brand-gradient) !important;\n";
            $css .= "    border: none !important;\n";
        } else {
            $css .= "    background-color: var(--custom-accent) !important;\n";
            $css .= "    background: var(--custom-accent) !important;\n";
            $css .= "    border-color: var(--custom-accent) !important;\n";
        }
        $css .= "    color: var(--text-light) !important;\n";
        $css .= "}\n\n";

        $css .= ".btn-brand:hover {\n";
        if ($settings['use_gradients'] == 1) {
            $css .= "    background: var(--custom-brand-gradient) !important;\n";
            $css .= "    background-image: var(--custom-brand-gradient) !important;\n";
            $css .= "    filter: brightness(0.9) !important;\n";
        } else {
            $css .= "    background-color: var(--custom-accent) !important;\n";
            $css .= "    background: var(--custom-accent) !important;\n";
            $css .= "    border-color: var(--custom-accent) !important;\n";
            $css .= "    filter: brightness(0.9) !important;\n";
        }
        $css .= "}\n\n";

        // Additional gradient applications
        $css .= ".card-header, .modal-header {\n";
        if ($settings['use_gradients'] == 1) {
            $css .= "    background: var(--custom-brand-gradient) !important;\n";
            $css .= "    background-image: var(--custom-brand-gradient) !important;\n";
            $css .= "    border: none !important;\n";
        } else {
            $css .= "    background-color: var(--custom-accent) !important;\n";
            $css .= "    background: var(--custom-accent) !important;\n";
            $css .= "    border-color: var(--custom-accent) !important;\n";
        }
        $css .= "    color: var(--text-light) !important;\n";
        $css .= "}\n\n";

        $css .= ".badge-primary, .badge-brand {\n";
        if ($settings['use_gradients'] == 1) {
            $css .= "    background: var(--custom-accent-gradient) !important;\n";
            $css .= "    background-image: var(--custom-accent-gradient) !important;\n";
            $css .= "    border: none !important;\n";
        } else {
            $css .= "    background-color: var(--custom-accent) !important;\n";
            $css .= "    background: var(--custom-accent) !important;\n";
            $css .= "    border-color: var(--custom-accent) !important;\n";
        }
        $css .= "    color: var(--text-light) !important;\n";
        $css .= "}\n\n";

        $css .= ".progress-bar {\n";
        if ($settings['use_gradients'] == 1) {
            $css .= "    background: var(--custom-accent-gradient) !important;\n";
            $css .= "    background-image: var(--custom-accent-gradient) !important;\n";
        } else {
            $css .= "    background-color: var(--custom-accent) !important;\n";
            $css .= "    background: var(--custom-accent) !important;\n";
        }
        $css .= "}\n\n";

        $css .= ".table-primary th, .table thead th {\n";
            if ($settings['use_gradients'] == 1) {
            $css .= "    background: var(--custom-brand-gradient) !important;\n";
            $css .= "    background-image: var(--custom-brand-gradient) !important;\n";
            $css .= "    border: none !important;\n";
        } else {
            $css .= "    background-color: var(--custom-accent) !important;\n";
            $css .= "    background: var(--custom-accent) !important;\n";
            $css .= "    border-color: var(--custom-accent) !important;\n";
        }
        $css .= "    color: var(--text-light) !important;\n";
        $css .= "}\n\n";

        return $css;
    }

    /**
     * Convert hex color to RGB values
     */
    private static function hexToRgb($hex)
    {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return "$r, $g, $b";
    }

    /**
     * Get theme CSS as a downloadable file
     */
    public static function getThemeCSSFile()
    {
        $css = self::generateThemeCSS();
        
        return response($css, 200, [
            'Content-Type' => 'text/css',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Get cache buster for theme assets
     */
    public static function getCacheBuster()
    {
        return SystemSetting::getValue('theme_cache_buster', time());
    }

    /**
     * Force clear theme cache
     */
    public static function clearCache()
    {
        try {
            // Clear Laravel caches
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('view:clear');
            \Artisan::call('route:clear');
            
            // Update cache buster
            SystemSetting::setValue('theme_cache_buster', time(), 'string');
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to clear theme cache: ' . $e->getMessage());
            return false;
        }
    }
}
