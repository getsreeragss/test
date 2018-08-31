// 9*9 Sudoku Lies in Sudoku
var num = 0;
var sudoku =    [
                    [num,num,num,num,num,num,num,6,num],
                    [num,num,7,3,num,num,9,num,num],
                    [num,num,8,9,num,num,num,num,num],
                    [num,7,1,num,num,num,num,num,num],
                    [num,num,num,num,num,num,num,num,8],
                    [8,num,num,num,5,num,6,num,4],
                    [num,1,num,2,num,num,num,9,num],
                    [2,num,num,num,num,4,num,num,num],
                    [num,6,9,num,num,num,num,7,num],
                ]
var userInput = {};

// in userInput you can give row and cell value


function checkNumExists() {

  var errorMessage = [];

  for(var r=0; r<9; r++){  

    if(sudoku[r][userInput.cell] === user.num){  
        errorMessage.push(userInput.num +"Exits");
    }

    if(r === userInput.row){   
       for(var c=0; c<9; c++){  
          if(sudoku[r][c] === userInput.num){  
            errorMessage.push("No Exists");
          }
       }
    }

  }

  if(err.length){
    console.log("Wrong Answers given");
  } else {  
    console.log( "Right Place! Great Going");
  }

}