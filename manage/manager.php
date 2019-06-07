<style>
.tooltip {
    position: relative;
    display: inline-block;
}
.tooltip .tooltiptext {
    visibility: hidden;
    width: 500px;
    background-color: LightGray;
    padding: 10px;
    border-style: solid;
    border-width: 2px;
    /* Position the tooltip */
    position: absolute;
    z-index: 1;
    top: -5px;
    left: 105%;
}
.tooltip:hover .tooltiptext {
    visibility: visible;
}
</style>

<div style="max-width:500px;padding:20px;margin:20px">

<?php
$addon = array();
$addon["id"] = $_POST["id"];
?>

<form action="/manage/index.php" method="post">
Load existing: <input type="text" name="id" placeholder="Addon ID" required>
<input type="submit" value="Load">
<?php if(isset($_POST["id"]))
{
    if(!isset($_POST["iderror"]))
    {
        echo "Loaded addon: " . $_POST["id"];
    }
    else
    {
        echo $_POST["iderror"];
    }
}
?>
</form>

<br><br>

<form action="/manage/process.php" method="post"><fieldset>
<legend>Edit addon</legend>

Name <input type="text" name="id" placeholder="Addon ID" style="float:right;" autofocus required value="<?php echo $addon["id"] ?>">
<br><br>

Author <input type="text" name="author" placeholder="Addon author" style="float:right;" required value="<?php echo $addon["author"] ?>">
<br><br>

Plugin ID
<div class="tooltip">(?)
    <span class="tooltiptext">Found in the header of the plugin's AlliedModders thread.</span>
</div>
<br>
<input type="radio" name="forumid" value="known" checked><input type="number" min="1" placeholder="Plugin ID" name="forumid_num"/><br>
<input type="radio" name="forumid" value="extension">Extension<br>
<input type="radio" name="forumid" value="unspecified">Unspecified<br>
<br>

Base URL
<div class="tooltip">(?)
    <span class="tooltiptext">
        <p>URL which will be searched for the specified files.</p>
        <p>Official AlliedModders forums and GitHub are treated individually. Full information and examples on how to choose the base URL can be found here TODO.</p>
        <p>In case of distributing the addon on the AlliedModders forums, please make sure the URL is the post containing the post with the attachments only, not the entire thread (for simplicity purposes).</p>
    </span>
</div>
<input type="url" name="url" placeholder="URL searched for files" style="width:100%" required>
<br><br>

Files
<div class="tooltip">(?)
    <span class="tooltiptext"><ul>
        <li>Separated by newline.</li>
        <li>Relative to <span style="font-family:monospace;background-color:DarkGray">(mod)/addons/sourcemod/</span>.</li>
        <li>Must be the <span style="font-family:monospace;background-color:DarkGray">(path)/(file)</span> format, for file at root use <span style="font-family:monospace;background-color:DarkGray">./(file)</span>.</li>
        <li>May go up two directories at most (up to <span style="font-family:monospace;background-color:DarkGray">(mod)/</span>).</li>
        <li>Archives are extracted preserving their inner directory structure.</li>
        <li>Do not include directories.</li>
        <li>View cohesive examples here TODO.</li>
    </ul></span>
</div>
<textarea name="files" cols="40" rows="5" placeholder="plugins/thriller.smx&#10;gamedata/thriller.plugin.txt&#10;plugins/AdvancedInfinteAmmo.smx&#10;./funcommandsX_.*.zip&#10;../../.*" style="width:100%" required></textarea>
<br><br>

<input type="submit" value="Add/Update">
</fieldset></form>

<form method="post"><fieldset style="background-color:Crimson;">
<legend style="background-color:white;">Delete addon</legend>
<input type="text" name="delete" placeholder="Addon ID to delete" required>
<input type="submit" value="Delete WITHOUT confirmation">
</fieldset></form>

<br><br>

Logged in as <?php echo $steamprofile["personaname"] . " -- " . $_SESSION["steamid"]; ?>

<div style="float:right;"><?php logoutbutton(); ?></div>

</div>
