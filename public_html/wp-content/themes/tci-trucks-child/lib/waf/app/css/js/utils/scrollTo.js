import { scrollSpeed } from '../config';
import { isMobile } from './isMobile';
const $ = jQuery.noConflict();
export const scrollTo = field => {
    const $field = $('[data-uuid=' + field.uuid + ']');
    // d('field',$field[0]);
    if ($field.length) {
        const scrollTop = $field.position().top;
        if (isMobile()) $('html,body').scrollTop(scrollTop);
        else $('html,body').animate({ scrollTop }, scrollSpeed);
    }
}