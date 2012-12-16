(function(a){
    a.fn.webwidget_tip_imagesAdSense=function(p){
        var p=p||{};

        var b=p&&p.tip_button_style?p.tip_button_style:"notepaper";
        var c=p&&p.tip_content_background_color?p.tip_content_background_color:"#FFF";
        var e=p&&p.tip_content_width?p.tip_content_width:"250";
        var position_data = p&&p.position_data?p.position_data:"";
        var content_border_color=p&&p.tip_content_border_color?p.tip_content_border_color:"#666";
        var tip_content_font_color=p&&p.tip_content_font_color?p.tip_content_font_color:"#666";
        var tip_button_autohide = p&&p.tip_button_autohide?p.tip_button_autohide:"enable";
        var d=p&&p.directory?p.directory:"img";
        var f="";
        var g=a(this);
        var image_offset;
        var image_width = g.width();
        var image_height = g.height();
        var all_box;
        var tip_button_image;
        var tip_button_image_over;
        var c_box = $("#"+g.attr("id")+"_content");
        var fade_timeout;
        switch(b){
            case'notepaper':
                tip_button_image = d+"/pin.png";
                tip_button_image_over = d+"/pin-over.png";
                break;
            case'exclamation':
                tip_button_image = d+"/exclamation.png";
                tip_button_image_over = d+"/exclamation-over.png";
                break;
            case'circle':
                tip_button_image = d+"/circle.png";
                tip_button_image_over = d+"/circle-over.png";
                break;
            case'interrogation':
                tip_button_image = d+"/touming.png";
                tip_button_image_over = d+"/touming2.png";
                break;
            default:
                break
        }
        
        e=parseInt(e);
        
        init();
        
        all_box.hover(
            function(){
                $(this).children(".button_box").fadeIn();
            },
            function(){
                if(tip_button_autohide == 'enable'){
                    $(this).children(".button_box").fadeOut();
                }
            }
        );
        all_box.children(".button_box").children("img").hover(
            function(){
                $(this).attr("src",tip_button_image_over);
                index = all_box.children(".button_box").children("img").index($(this));
                all_box.children(".show").children(".show_content").html(c_box.children("li").eq(index).html());
                all_box.children(".show").css({ top: (parseInt(position_data[index].y)+70)+"px", left: (parseInt(position_data[index].x)+30)+"px" });
                all_box.children(".show").fadeIn();
                
            },
            function(){
                
                $(this).attr("src",tip_button_image);
                fade_timeout = setTimeout(fadeout,70);
                
            }
        );
        all_box.children(".show").hover(
            function(){
                clearTimeout(fade_timeout);
            },
            function(){
                $(this).fadeOut();
            }
        );
        function fadeout(){
            all_box.children(".show").fadeOut();
        }
        function init(){
            image_offset = g.offset();
            g.wrap('<div class="all_box"></div>');
            all_box = g.parent(".all_box");
            all_box.css({position:"relative",background:"url("+d+"/space.gif)",width:image_width+"px",height:image_height+"px"});
            all_box.append('<div class="button_box" style="position:absolute;top:0px;left:0px;width:'+image_width+'px;height:'+image_height+'px;"></div>');
            $.each(position_data,function(entryIndex,entry){
                all_box.children(".button_box").append('<img src="'+tip_button_image+'" style="position:absolute;cursor:pointer;top:'+entry['y']+'px;left:'+entry['x']+'px" />');
            });
            c_box.hide();
            all_box.append('<div class="show" style="position:absolute;"><div class="show_content" style="width:'+(e-8)+'px;background-color:'+c+';border-radius: 7px;border1px solid #fff;-moz-border-radius:5px;font-size:11px;font-family:Arial,"微软雅黑";color:'+tip_content_font_color+';padding:4px;border:1px '+content_border_color+' solid;"></div></div>');
            if(tip_button_autohide == 'enable'){
                all_box.children(".button_box").hide();
            }
            all_box.children(".show").hide();
        }
        
    }
})(jQuery);