const $ = jQuery;
export const parseJSON = str => {
	if (typeof (str) == 'string') {
		str = ` ${str} `
		const match = str.match(/\{(.*)\}/g);
		let data = match ? match.pop() : '';
		const debug = match ? str.replace(data, '') : str + ' ';

		try { data = JSON.parse(data) } catch (e) {
			// d('[parseJSON]::[Error]',"\n=====================\n",
			// e.message,data
			// ,"\n=====================\n"
			// ); 
		}
		return { data, debug }
	}
	else return { data: str, debug: '' };

}
