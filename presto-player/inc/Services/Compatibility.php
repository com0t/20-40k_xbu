<?php

namespace PrestoPlayer\Services;

class Compatibility
{
    public function register()
    {
        // wp rocket compat
        add_action('rocket_exclude_js', [$this, 'excludeComponentsFile']);

        // siteground optimize
        add_action('sgo_js_minify_exclude', [$this, 'excludeHandle']);

        // godaddy's shitty feedback modal
        add_action('admin_enqueue_scripts', [$this, 'goDaddyModal'], 99);

        // allow our player html
        add_filter('wp_kses_allowed_html', [$this, 'allowHtml'], 11);
    }

    /**
     * Lets us use our player tag in content. 
     *
     * @param  array $tags Allowed tags.
     * @return array
     */
    public function allowHtml($tags)
    {
        $tags['presto-player'] = [
            'direction' => true,
            'css' => true,
            'skin' => true,
            'icon-url' => true,
            'id' => true,
            'src' => true,
            'css' => true,
            'class' => true,
            'preload' => true,
            'poster' => true,
            'playsinline' => true,
            'autoplay' => true,
        ];
        return $tags;
    }

    public function goDaddyModal()
    {
        global $post_type;
        if ('pp_video_block' == $post_type) {
            wp_dequeue_script('nextgen-feedback-modal');
        }
    }

    /**
     * Exclude module by file
     *
     * @param array $excluded_js
     * @return array
     */
    public function excludeComponentsFile($excluded_js)
    {
        $excluded_js[] = str_replace(home_url(), '',  PRESTO_PLAYER_PLUGIN_URL . "dist/components/web-components/web-components.esm.js");

        return $excluded_js;
    }

    /**
     * Exclude module by handle
     *
     * @param array $handles
     * @return array
     */
    public function excludeHandle($handles)
    {
        $handles[] = 'presto-components';
        return $handles;
    }
}
