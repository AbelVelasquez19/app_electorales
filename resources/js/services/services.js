import axios from 'axios';
const getListInfo = async (query,url,pageSize) => {
    try {
        let dataList = [];
        const response = await axios.get(url,{
            params: { q: query,pageSize:pageSize },
        });
        dataList.push(response.data);
        return dataList;
    } catch (error) {
        return error = {
            status:'500',
        };
    }
}
const getAll = async (url) => {
    try {
        let result = {};
        const response = await axios.get(url);
        return result = response.data;
    } catch (error) {
        return error = {
            status:'500',
        };
    }
}
const getId = async (url,id) => {
    try {
        let result = {};
        const response = await axios.get(url,{
            params:id,
        });
        return result = response.data;
    } catch (error) {
        return error = {
            status:'500',
        };
    }
}
const getShowInfo = async (url,id) => {
    try {
        let result = {};
        const response = await axios.post(url,{
            params:id,
        });
      
        return result = response.data;
    } catch (error) {
        return error = {
            status:'500',
        };
    }
}
const addNewInfo = async(url,obj) => {
    try {
        let result = [];
        const response = await axios.post(url,obj);
        console.log(response.data);
        result.push(response.data);
        return {
            status:true,
            result:result
        };
    }catch (error) {
        return {
            status: false,
            result: error.response.data.errors
        }
    }
}

export default {
    getListInfo,
    addNewInfo,
    getShowInfo,
    getAll,
    getId
}