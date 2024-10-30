                jQuery.noConflict();
                function wiziappappwall_position_appwall()
                {
                    (function( $ ) {
                            if( $('.wiziappappwall-display').length )         // use this if you are using class to check
                            {
                                    $('body').children(':not(.wiziappappwall-frame)').map(function () { 
                                        if($(this).is(':visible')) {
                                            $(this).attr("wiziappappwall-hide",true);
                                            $(this).hide();
                                        }
                                    });
                                    //$('body > :not(.wiziappappwall-frame)').hide();
                                    $('.wiziappappwall-display').remove();
                                    $('.wiziappappwall-frame').offset({ top: 0, left: 0 });
                                    $('.wiziappappwall-frame').show();                                    
                            }
                    })(jQuery);                    
                }

                function wiziappappwall_hide_appwall()
                {
                    (function( $ ) {
                        $('body').children(':not(.wiziappappwall-frame)').map(function () { 
                            if ($(this).attr('wiziappappwall-hide')) {
                                $(this).show();
                                $(this).removeAttr("wiziappappwall-hide");
                                $(this).trigger('isVisible');
                            } 
                        });
                        //$('body > :not(.wiziappappwall-frame)').hide();
                        $('.wiziappappwall-frame').hide();                                    
                    })(jQuery);                    
                }

                function wiziappappwall_autoIframe() {
                    (function( $ ) {
                        
                        var elements = document.getElementsByClassName('wiziappappwall-iframe');

			for (var i = 0; i < elements.length; i++) {
                            frame = elements[i];
                            innerDoc = (frame.contentDocument) ? frame.contentDocument : frame.contentWindow.document;
                            objToResize = (frame.style) ? frame.style : frame;
                            objToResize.height = innerDoc.body.scrollHeight + 10 + 'px';
			}                        
                    })(jQuery);                    
                }
                
                function wiziappappwall_pageshow( event, ui ) {
                    (function( $ ) {
                        $(".wiziappappwall-internal-frame").map(function () { 
                                var currParent = $(this).parent();
                                var moveIt = $(this).remove();
                                currParent.append(moveIt);
                        });
                        wiziappappwall_position_appwall();
                        wiziappappwall_autoIframe();
                    })(jQuery);                    
                }

                (function( $ ) {
                    $(function() {
                            $("body").delegate('.wiziappappwall-skip','click', wiziappappwall_hide_appwall);                                 
                            $('.wiziappappwall-skip').click(wiziappappwall_hide_appwall);
                            $("body").delegate('[data-role=page]','pageshow', wiziappappwall_pageshow);
                            wiziappappwall_position_appwall();
                            $(".wiziappappwall-internal-frame").map(function () { 
                                var currParent = $(this).parent();
                                var moveIt = $(this).remove();
                                currParent.append(moveIt);
                            });
                            $("body").delegate('.wiziappappwall-iframe','load', wiziappappwall_autoIframe);
                    });
                })(jQuery);