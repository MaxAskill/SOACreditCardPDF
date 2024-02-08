<?php
// include ("submit.php");
?>
<div class="container">
    <title>CREDIT CARD SOA DATA EXTRACTOR</title>
    <div class=" main-container">
    <div class="title-bar">
        <h1 >CREDIT CARD SOA DATA EXTRACTOR</h1>
    </div>
    <form action="submit-test.php" method="POST" enctype="multipart/form-data">
        <div class="form-input">
            <label for="images" class="drop-container" id="dropcontainer">
                <span class="drop-title">Drop files here</span>
                or
                <!-- <input type="file" id="images" accept="image/*" required> -->
                        <input type="file" id="PDFfile" name="pdf_file" accept="application/pdf" placeholder="Select a PDF file" required="">
            </label>

            <label id="error_file" style="display:none; color: red; padding: 5px"> PDF Only </label>

            <!-- <label for="pdf_file">PDF File</label>
            <input type="file" name="pdf_file" placeholder="Select a PDF file" required=""> -->
        </div>

        <div class="submit-bar">
            <input class="submit-button" type="submit" name="submit" class="btn" value="Extract Data">
        </div>
    </form>
    </div>
</div>

<script>

const dropContainer = document.getElementById("dropcontainer")
  const fileInput = document.getElementById("PDFfile")

  dropContainer.addEventListener("dragover", (e) => {
    // prevent default to allow drop
    e.preventDefault()
  }, false)

  dropContainer.addEventListener("dragenter", () => {
    dropContainer.classList.add("drag-active")
  })

  dropContainer.addEventListener("dragleave", () => {
    dropContainer.classList.remove("drag-active")
  })

  dropContainer.addEventListener("drop", (e) => {
    e.preventDefault()
    console.log("Transfered File: ", e.dataTransfer.files[0].name);
    if(e.dataTransfer.files[0].name.split(".")[1] == "pdf"){
    dropContainer.classList.remove("drag-active")
    fileInput.files = e.dataTransfer.files
    document.getElementById("error_file").style.display = "none";
        dropContainer.style.background = "#CDFFDF";
        dropContainer.style.border = "2px solid #00B643";
    }
    else{
        dropContainer.style.background = "#FFCDCD";
        dropContainer.style.border = "2px solid #B60000";
        document.getElementById("error_file").style.display = "block";
    }
  })
</script>


<style>
    .main-container{
  font-family: Arial, Helvetica, sans-serif;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  padding: 10px;
    }
    
    .title-bar{
    display: flex;
        justify-content: center;
    }
    .submit-bar{
    display: flex;
        justify-content: center;
        margin-top: 10px;
    }

    .submit-button{
  width: 300px;
  max-width: 100%;
  color: #00B643;
  padding: 15px;
  background: #CDFFDF;
  border-radius: 10px;
  border: 2px solid #00B643;
  cursor: pointer;
  font-weight: bold;
    }

    .submit-button:hover{
  background: #00B643;
  font-weight: bold;
  color: #fff;
    }
.drop-container {
  position: relative;
  display: flex;
  gap: 10px;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  height: 200px;
  padding: 20px;
  border-radius: 10px;
  border: 2px dashed #555;
  color: #444;
  cursor: pointer;
  transition: background .2s ease-in-out, border .2s ease-in-out;
}

.drop-container:hover,
.drop-container.drag-active {
  background: #CDFFDF;
  border: 2px solid #00B643;
}

.drop-container:hover .drop-title,
.drop-container.drag-active .drop-title {
  color: #222;
}

.drop-title {
  color: #444;
  font-size: 20px;
  font-weight: bold;
  text-align: center;
  transition: color .2s ease-in-out;
}

input[type=file] {
  width: 350px;
  max-width: 100%;
  color: #444;
  padding: 5px;
  background: #fff;
  border-radius: 10px;
  border: 1px solid #555;
}

input[type=file]::file-selector-button {
  margin-right: 20px;
  border: none;
  background: #084cdf;
  padding: 10px 20px;
  border-radius: 10px;
  color: #fff;
  cursor: pointer;
  transition: background .2s ease-in-out;
}

input[type=file]::file-selector-button:hover {
  background: #0d45a5;
}
</style>