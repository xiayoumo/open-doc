import axios from 'axios'

var postUrl = DocConfig.server;
export async function post_(url, data, callback, err){
    var qs = require("querystring")
    data = qs.stringify(data);
    await axios.post(postUrl+url,data,{async:false})
                .then(response => {
                    callback(response.data)
                })
                .catch(function (error){
                    console.log(error);
            })
}
