const $ = jQuery;
export const parseJSON = str => {
	if (typeof (str) == 'string') {
		let data, debug;
		try {
			data = JSON.parse(str);
		} catch( e ) {

		}
		str = ` ${str} `
		
		if( !data) { 
			try { 
				const match = str.match(/\{(.*)\}/g);
				let data = match ? match.pop() : '';
				debug = match ? str.replace(data, '') : str + ' ';
				data = JSON.parse(data)
				return {data,debug}
			} catch (e) {
				data = {}
			}
		}
		return { data, debug }
	}
	else return { data: str, debug: '' };

}
