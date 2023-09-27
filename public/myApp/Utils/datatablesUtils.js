function drawTable(table_name, lData){
    table[table_name].clear().draw();
    table[table_name].rows.add(lData).draw();
}

function drawTableJson(nameTable, jsonData, ...keys){
    if(jsonData != null){
        try {
            var arrayData = [];
            
            jsonData.forEach(function(item) {
                var newItem = [];
                
                keys.forEach(function(key) {
                    newItem.push(item[key]);
                });
                
                arrayData.push(newItem);
            });
    
            table[nameTable].clear().draw();
            table[nameTable].rows.add(arrayData).draw();
        } catch (error) {
            // location.reload();
            console.log(error);
        }
    }else{
        table[nameTable].draw();
    }
}

function searchIndex(table_name, columns, values){
    var indexes = table[table_name].rows().indexes().filter(function (value, index) {
        let result = true;
        for(let [i, col] of columns.entries()){
            result = result && (values[i] == table[table_name].column(col).data()[value]);
            if(!result){
                break;
            }
        }

        return result;
    });

    var indice = indexes[0];

    return indice;
}