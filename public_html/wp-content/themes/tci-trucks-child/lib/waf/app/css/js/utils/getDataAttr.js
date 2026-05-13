const Handlebars = require('handlebars');

const getDataAttr = context => {
    const { data, conditions } = context;

    let dataAttr = `data-id="${context.name}"`;
    if (data && typeof (data) === 'object') {
        dataAttr += Object.entries(data).reduce((dataAttr, [attr, value]) => {
            return dataAttr += ` data-${attr}="${value}"`;
        }, '');
    }

    if (conditions && typeof (data) === 'object') {
        dataAttr += Object.entries(conditions).reduce((dataAttr, [attr, value]) => {
            return dataAttr += ` data-condition-${attr}="${value}"`;
        }, '');
    }
    // d('get data attr',dataAttr);
    context.dataAttr = dataAttr;
}

module.exports = { getDataAttr }