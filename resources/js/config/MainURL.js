let prod_base_url = 'https://192.231.237.29:8888/zpc/public';
let dev_base_url  = 'https://192.231.237.29:8888/zpc/public';
let prod_file_url = 'https://192.231.237.29:8888/zpc/public';
let dev_file_url = 'https://192.231.237.29:8888/zpc/public';
let prod_web_url = 'https://192.231.237.29:8888/zpc/public';
let dev_web_url = 'https://192.231.237.29:8888/zpc/public';

const BASE_URL = process.env.MIX_APP_ENV === 'production' ? prod_base_url : dev_base_url;
const FILE_URL = process.env.MIX_APP_ENV === 'production' ? prod_file_url : dev_file_url;
const WEB_URL = process.env.MIX_APP_ENV === 'production' ? prod_web_url : dev_web_url;

export {BASE_URL, FILE_URL, WEB_URL};

