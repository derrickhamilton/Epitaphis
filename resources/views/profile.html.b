{% include 'templates/header.html' %}
{% include 'templates/navbar.html' with nav %}
<body>
    <div id="signup-panel" class="container">
        <div class="panel panel-primary">
            <div class="panel-heading">Change your information</div>
            <div class="panel-body">
                <script>
                    var boxId = 1;
                    function addField(e)
                    {
                        var end = document.getElementById("test");
                        var inputGoalName = document.createElement("input");
                        var inputGoalText = document.createElement("input");
                        inputGoalName.type="text";
                        inputGoalText.type="text";
                        inputGoalName.name="goal_name_new"+boxId;
                        inputGoalText.name="goal_text_new"+boxId;
                        boxId++;
                        end.appendChild(document.createElement("br"));
                        end.appendChild(inputGoalName);
                        end.appendChild(inputGoalText);
                        end.appendChild(document.createElement("br"));
                    }
                    function removeGoal(e)
                    {
                        var target = document.getElementById(e.target.id);
                        var targetId = target.id.substring(11);
                        var goal_name = document.getElementById("goal_name"+targetId);
                        var goal_text = document.getElementById("goal_text"+targetId);
                        var goal_button = document.getElementById("goal_button"+targetId);
                        if (target.value == "remove")
                        {
                            goal_name.disabled=false;
                            goal_text.disabled=false;
                            goal_name.style="";
                            goal_text.style="";
                            goal_button.value="";
                        }
                        else
                        {
                            var removeGoal = document.createElement("input");
                            removeGoal.type="hidden";
                            removeGoal.name="goal_remove"+targetId;
                            target.appendChild(removeGoal);
                            goal_name.disabled=true;
                            goal_name.style="background: #CCC;";
                            goal_text.disabled=true;
                            goal_text.style="background: #CCC;";
                            goal_button.value="remove";
                        }
                    }
                </script>
                <form method="post" action="/Epitaphis/profile.php">
                    <p>
                        <label class="form-label">Bio:</label><input type="text" name='bio' placeholder="Tell us about yourself" value='{{ bio }}'></input>
                        <br>
                        <label class="form-label">Goals:</label>
                        <div id='test'>
                        {% for row in rows %}
                            <br>
                            <input type="text" id='goal_name{{ row['goal_id'] }}' name='goal_name{{ row['goal_id'] }}' value='{{ row['goalName'] }}'></input>
                            <input type="hidden" name='goal_id{{ row['goal_id'] }}' value='{{ row['goal_id'] }}'/>
                            <input type="text" id='goal_text{{ row['goal_id'] }}' name='goal_text{{ row['goal_id'] }}' value='{{ row['goalText'] }}'/>
                            <button type="button" id='goal_button{{ row['goal_id'] }}' onclick='removeGoal(event)'>Remove</button>
                            <br>
                        {% endfor %}
                        </div>
                    </p>
                    <button type="button" onclick='addField(event)'>Add a goal</button>
                    <div class="submit-button"><input class="btn btn-primary btn-block" type="submit" value="Submit" /></div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

