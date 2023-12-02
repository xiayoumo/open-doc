import { post_ } from '@/js/request.js';
import axios from "axios";

export let uploadUrl = DocConfig.server+'/api/page/uploadImg';

export default {
    //开启同步请求（异步请求会导致取不到数据）
    async execPost(postUrl = '', postCondision = {}) {
        // this.loading = true;
        let postData = {};
        await post_(postUrl, postCondision,(res) => {
                //须单个赋值，直接赋值无法传递出去
                postData = res;
                // if (res.error_code == 0) {
                //     //须单个赋值，直接赋值无法传递出去
                //     postData = res;
                // } else {
                //     //停止后续的执行，外层捕获报错
                //     return new Promise(()=>{});
                // }
            }).catch(()=>{
            //停止后续的执行，外层捕获报错
            return new Promise(()=>{})
        })
        return postData;
    },
    //开启同步请求（异步请求会导致取不到数据）
    async execUpload(fileData={}) {
        let postData = {};
        let postObjectData = fileData;
        var formdata = new FormData();
        formdata.append("editormd-image-file", postObjectData,"image.png");
        await axios.post(uploadUrl, formdata, {async:false}).then(res => {
            //  console.log(res);
            //  console.log(data.file)
            if (res.data.success == 1) {
                postData = res;
            } else {
                this.$message.error(res.data.error_message);
            }
        })
        return postData;
    },
    /**
     * get 请求工具
     * @param getUrl
     * @param getCondisionObject
     * @returns {Promise<{}>}
     */
    async execGet(getUrl = '', getCondisionObject = {}) {
        let getUrlByCondision = this.setGetCondisionByObject(getUrl,getCondisionObject);
        let getData = {};
        await axios.get(getUrlByCondision,(res) => {
            if (res.error_code == 0) {
                //须单个赋值，直接赋值无法传递出去
                getData = res;
            } else {
                //停止后续的执行，外层捕获报错
                return new Promise(()=>{});
            }
        }).catch(()=>{
            //停止后续的执行，外层捕获报错
            return new Promise(()=>{})
        })
        return getData;
    },
    /**
     * 按url获取文件内容
     * @param getUrl
     * @returns {Promise<string>}
     */
    async getFileDataByUrl(getUrl = '') {
        let getData = {};
        await axios.get(getUrl).then(function(response) {
            getData = response.data;
        }).catch(function(err) {
            //停止后续的执行，外层捕获报错
            return new Promise(()=>{})
        });
        return getData;
    },
    /**
     * 设置 get 参数
     * @param uploadJobFileUrl
     * @param getObject
     * @returns {string}
     */
    setGetCondisionByObject(getUrl='',getObject={}){
        let getString = this.getCondisionStringByObject(getObject);
        if(getString){
            getUrl += '?' + getString;
        }
        return getUrl;
    },
    /**
     * 对象转get参数
     * @param getObject
     * @returns {string}
     */
    getCondisionStringByObject(getObject={}){
        let getStringArray = [];
        for (const key in getObject) {
            getStringArray.push(key + '=' + getObject[key]);
        }
        return getStringArray.join('&');
    },
    /**
     *  img的src(base64)内容转化为可以http上传的file 对象
     * @param imgSrc
     * @returns {Blob}
     */
    getUploadFormByImgSrc(imgSrc=''){
        //上传到后端
        var tempArray = imgSrc.split(','), mime = tempArray[0].match(/:(.*?);/)[1],
            bstr = atob(tempArray[1]), n = bstr.length, u8arr = new Uint8Array(n);
        while(n--){
            u8arr[n] = bstr.charCodeAt(n);
        }
        return new Blob([u8arr], {type:mime});
    },
    /**
     * 按图片上传提交的data中的file参数，获取可展示的img src
     * @param data_file
     * @returns {string}
     */
    getImgSrcByUploadFile(data_file={}){
        let urlCreator = window.URL || window.webkitURL;
        return urlCreator.createObjectURL(data_file);
    }

}

