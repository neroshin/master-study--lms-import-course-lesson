<div class="wrap">
    <br/><h1>Import CSV Course</h1>

    <div id="response"
        class="<?php if(!empty($type)) { echo $type . " display-block"; } ?>">
        <?php if(!empty($message)) { echo $message; } ?>
        </div>
    <div class="outer-scontainer">
        <div class="row">

            <form class="form-horizontal" 
				action="<?php echo esc_url( admin_url('admin-post.php') ); ?>"
				method="post"
                name="frmCSVImport" id="frmCSVImport"
                enctype="multipart/form-data">
                <div class="input-row">
                    <label class="col-md-4 control-label">Import CSV Course File</label> 
					
					<input type="file" name="upload_csv" id="file" accept=".csv">
                    <button type="submit" id="submit" name="import" value="import" class="btn-submit">Import</button>
                    <br />
					 <input type="hidden" name="action" value="import_csv_lms_course">
                </div>

            </form>

        </div>
</div>


<style>
    .update-nag, .updated, .error, .is-dismissible {
        display: none;
    }
    body {
    font-family: Arial;
    /* width: 550px; */
}

.outer-scontainer {
    /* background: #F0F0F0;
    border: #e0dfdf 1px solid;
    padding: 20px;
    border-radius: 2px; */
}

.input-row {
    margin-top: 0px;
    margin-bottom: 20px;
}

.btn-submit {
    background: #333;
    border: #1d1d1d 1px solid;
    color: #f0f0f0;
    font-size: 0.9em;
    width: 100px;
    border-radius: 2px;
    cursor: pointer;
}

.outer-scontainer table {
    border-collapse: collapse;
    width: 100%;
}

.outer-scontainer th {
    border: 1px solid #dddddd;
    padding: 8px;
    text-align: left;
}

.outer-scontainer td {
    border: 1px solid #dddddd;
    padding: 8px;
    text-align: left;
}

#response {
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 2px;
    display: none;
}

.success {
    background: #c7efd9;
    border: #bbe2cd 1px solid;
}

.error {
    background: #fbcfcf;
    border: #f3c6c7 1px solid;
}

div#response.display-block {
    display: block;
}
</style>