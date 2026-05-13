export const pathToName = (path = '') => {
    // d('path', path);
    const parts = path.split('.');
    const first = parts.shift();
    // d(parts, first);
    const name = parts.length ? first + '[' + parts.join('][') + ']' : first;
    // d('name attr:', name);
    return name;
}