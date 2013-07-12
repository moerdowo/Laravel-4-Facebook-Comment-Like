<?php namespace Totox777\Fbplugins;

class Fbplugins extends JxFacebookObject {

        public function __construct ( $url = '' ) {
            parent::__construct ( $url ) ;
        }

        public static function getInstance ( $url = '' ) {
            static $instance ;
            if ( !isset ( $instance ) ) {
                $instance = new self ( $url ) ;
            }
            return $instance ;
        }

        public function getSdk ( $appId , $secret ) {
            require_once 'sdk/facebook.php' ;
            return new Facebook ( array ( 'appId' => $appId , 'secret' => $secret , ) ) ;
        }

        public function getScript ( $langTag = 'en_US' ) {
            $script = '<div id="fb-root"></div>
            <script>
                (function(d, s, id) {
                    var js, fjs = d.getElementsByTagName(s)[0];
                    if (d.getElementById(id)) return;
                    js = d.createElement(s); js.id = id;
                    js.src = "//connect.facebook.net/' . $langTag . '/all.js#xfbml=1&appId=' . $this->getAppId () . '";
                    fjs.parentNode.insertBefore(js, fjs);
               }(document, \'script\', \'facebook-jssdk\'));
            </script>' ;
            return $script ;
        }

        public function loadScript ( $langTag = 'en_US' ) {
            static $loaded = false ;
            if ( $loaded == false ) {
                $script = '
                <div id="fb-root"></div>
                <script>
                    if (typeof window.FB === "undefined") {
                        window.fbAsyncInit = function() {
                            FB.init({

                            });

                            // Additional initialization code here
                        };

                        // Load the SDK Asynchronously
                        (function(d){
                            var js, id = \'facebook-jssdk\', ref = d . getElementsByTagName(\'script\')[0];
                            if (d . getElementById(id)) {
                                return;
                            }
                            js = d . createElement(\'script\');
                            js.id = id;
                            js.async = true;
                            js.src = "//connect.facebook.net/' . $langTag . '/all.js#xfbml=1&appId=' . $this->getAppId () . '";
                            ref . parentNode . insertBefore(js, ref);
                        }(document));
                    }
                </script>' ;
                $loaded = true ;
            } else {
                $script = '' ;
            }
            return $script ;
        }

        public function getHtml () {
            $type = $this->getOpenGraph ( 'type' , array ( 'website' ) ) ;
            $html = 'xmlns:fb = "http://ogp.me/ns/fb#"' ;
            $html .='prefix = "og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# ' . $type[0] . ': http://ogp.me/ns/' . $type[0] . '# "' ;
            return $html ;
        }

        public function getHead () {
            $head = "\n" ;
            foreach ( $this->opengraph as $key => $opengraph ) {
                $prev = '' ;
                foreach ( $opengraph as $value ) {
                    if ( $value != '' && $value != $prev ) {
                        $head .= '<meta property = "' . $key . '" content = "' . $value . '" />' . "\n" ;
                        $prev = $value ;
                    }
                }
            }
            $head .= '<meta property = "fb:admins" content = "' . $this->getAdmins () . '" />' . "\n" ;
            $head .= '<meta property = "fb:app_id" content = "' . $this->getAppId () . '" />' . "\n" ;
            return $head ;
        }

        public function getPlugin ( $name ) {
            $class = 'jooxfb-' . $name . ' fb-' . $name ;
            $html = '<div class = "' . $class . '"' ;
            foreach ( $this->attributes as $key => $value ) {
                $html .= 'data-' . $key . ' = "' . $value . '"' ;
            }
            $html .= '></div>' ;
            return $html ;
        }

        public function getDialog ( $dialog = 'feed' , $obj = array ( ) , $type = 'url' ) {
            $fbDialog = 'https://www.facebook.com/dialog/' . $dialog . '?' ;
            $defObject = array (
                'redirect_uri' => $this->url . '&fb_redirected' ,
                'display' => ($type == 'url') ? 'page' : 'popup' , /* https://developers.facebook.com/docs/reference/dialogs/#display */
                'from' => '' ,
                'to' => '' ,
                'link' => $this->url ,
                'picture' => '' ,
                'source' => '' ,
                'name' => '' ,
                'caption' => '' ,
                'description' => '' ,
                'properties' => '' ,
                'actions' => '' ,
                'ref' => ''
                    ) ;
            $obj = array_merge ( $obj , $defObject ) ;

            switch ( $type ) {
                case 'url' :
                    if ( $type == 'url' ) {
                        $url = 'app_id=' . $this->getAppId () ;
                        foreach ( $obj as $key => $value ) {
                            if ( $value != '' ) {
                                $url .= '&' . $key . '=' . urlencode ( $value ) ;
                            }
                        }
                        return $fbDialog . $url ;
                    }
                case 'json' :
                    return json_encode ( $obj ) ;
                case 'script' :
                    /* return javascript object use for FB.ui */
                    $script = '
                            var jxFacebookUi = {}
                        ' ;
                    break ;
            }
        }

        public function setProperties ( $data ) {
            if ( is_array ( $data ) ) {
                foreach ( $data as $name => $value ) {
                    $this->setAttribute ( $name , $value ) ;
                }
            }
        }

        public function setType ( $data ) {
            $this->setOpenGraph ( 'type' , $data ) ;
        }

        public function setTitle ( $data ) {
            $this->setOpenGraph ( 'title' , $data ) ;
        }

        public function setDescription ( $data ) {
            $this->setOpenGraph ( 'description' , $data ) ;
        }

        public function setSiteName ( $content ) {
            $this->setOpenGraph ( 'site_name' , $content ) ;
        }

        public function getArticle ( $title , $description , $image , $siteName , $article = array ( ) ) {
            $this->setType ( 'article' ) ;
            $this->setTitle ( $title ) ;
            $this->setDescription ( $description ) ;
            $this->setSiteName ( $siteName ) ;
            $this->setOpenGraph ( 'image' , $image ) ;
            foreach ( $article as $key => $value ) {
                $this->setOpenGraph ( $key , $value , 'article:' ) ;
            }
            return $this->getHead () ;
        }

        public function getBlog ( $title , $description , $image ) {
            $this->setType ( 'blog' ) ;
            $this->setTitle ( $title ) ;
            $this->setDescription ( $description ) ;
            $this->setOpenGraph ( 'image' , $image ) ;
            return $this->getHead () ;
        }

        public function getWebsite ( $title , $description , $image ) {
            $this->setType ( 'website' ) ;
            $this->setTitle ( $title ) ;
            $this->setDescription ( $description ) ;
            $this->setOpenGraph ( 'image' , $image ) ;
            return $this->getHead () ;
        }

}

class JxFacebookObject {

        protected $opengraph = array ( ) ; /* used in <head><meta> */
        protected $attributes = array ( ) ; /* use for Facebook plugins */
        protected $app_id = '129458053806961' ;
        protected $admins = '100002155587121' ;
        protected $url = '' ;

        public function __construct ( $url = '' ) {
            $this->url = $url ;
        }

        public static function getInstance ( $url = '' ) {
            static $instance ;
            if ( !isset ( $instance ) )
                $instance = new JxFacebook ( $url ) ;
            return $instance ;
        }

        public function setOpenGraph ( $name , $value , $prefix = 'og' ) {
            $this->opengraph[$prefix . ':' . $name][] = $value ;
        }

        public function cleanOpenGraph ( $name , $prefix = 'og' ) {
            $this->opengraph[$prefix . ':' . $name] = array ( ) ;
        }

        public function getOpenGraph ( $name , $def = '' , $prefix = 'og' ) {
            if ( isset ( $this->opengraph[$prefix . ':' . $name] ) ) {
                return $this->opengraph[$prefix . ':' . $name] ;
            }else
                return $def ;
        }

        public function setAttribute ( $name , $value ) {
            $this->attributes[$name] = $value ;
        }

        public function getAttribute ( $name , $def = '' ) {
            if ( isset ( $this->attributes[$name] ) ) {
                return $this->attributes[$name] ;
            }else
                return $def ;
        }

        public function setAppId ( $id ) {
            $this->app_id = $id ;
        }

        public function getAppId ( $def = '129458053806961' ) {
            if ( isset ( $this->app_id ) )
                return $this->app_id ;
            else
                return $def ;
        }

        public function setAdmins ( $id ) {
            $this->admins = $id ;
        }

        public function getAdmins ( $def = '100002155587121' ) {
            if ( isset ( $this->admins ) )
                return $this->admins ;
            else
                return $def ;
        }

        public function setUrl ( $url ) {
            $this->url = $url ;
            $this->setAttribute ( 'href' , $url ) ;
            $this->setOpenGraph ( 'og:url' , $url ) ;
        }

        public function getUrl () {
            return $this->url ;
        }

    }