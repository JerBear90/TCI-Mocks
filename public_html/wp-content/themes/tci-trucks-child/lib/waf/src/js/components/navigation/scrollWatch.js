const $ = jQuery.noConflict();
let oldScroll = 0;
export const scrollWatch = e => {
  
    const top = $(e.target).scrollTop();
    // This is then function used to detect if the element is scrolled into view
    function elementScrolled(elem){
      
      var docViewTop = $(window).scrollTop();
      var docViewBottom = docViewTop + $(window).height();
      var elemTop = $(elem).offset().top;
      
      // if( oldScroll > top ) docViewTop -= 100;
      // else 
      docViewTop += 100;
      // d('scroll direction', oldScroll > e.scrollY ? 'down' : 'up')
      return ((elemTop <= docViewBottom) && (elemTop >= docViewTop));
    }
  
    // This is where we use the function to detect if ".box2" is scrolled into view, and when it is add the class ".animated" to the <p> child element
    let halt = false;
    $('.waf-fullscreen-container .form-bd > .bd ').find('> .form-group, > fieldset').each( function() {
        if( halt ) return false;
        const form = $(this).getForm();
        if( elementScrolled( this ) ) {
          console.log( 'scroll check', elementScrolled( this ), $(this).getTopField() );
        
          form.active = $(this).getTopField()
          // if( form.active ) console.log('active',form.active.context.name);
          halt = true;
        }
    })
    oldScroll = top;
  }