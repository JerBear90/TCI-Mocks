const $ = require('../../utils/jquery');
const Handlebars = require('handlebars');
const wp = require('../../utils/wp');
const Form = require('../../inc/Form');

const renderFormsList = async (el) => {
    // console.log('Form config',window.formConfig)
    if (!window.formConfig) return false;
    // d('renderFormsList',formConfig.templates.view['form-list']);
    const container = formConfig.templates.view['form-list']
    // d('form config!!!!',formConfig);
    // let forms = await wp.forms();//.catch( e => false );

    const forms = Object.entries(formConfig.forms).map(([id, data]) => {
        d('data', data, 'id', id);
        const form = new Form(data);
        const uuid = form.uuid;

        window.forms[uuid] = form;
        data.uuid = form.uuid;
        data.json = null;
        data.slug = id;
        data.name = form.args.title;
        // data.status = 
        // d('form',data); 
        return data;
    });
    // d(forms,form);

    const html = Handlebars.compile(container)({ forms });;

    if (el) $(el).html(html);
    return html;
}
module.exports = { renderFormsList }