var supersetCounter = 1;

function editMuscle(muscleId, muscleName)
{
    $('form #muscle-id').val(muscleId);
    $('form #muscle-name').val(decodeURIComponent(muscleName)).attr('placeholder', decodeURIComponent(muscleName)).focus();
    $('#cancel-muscle-edit-button').removeClass('d-none');
}

function cancelMuscleEdit()
{
    $('form #muscle-id').val('');
    $('form #muscle-name').val('').attr('placeholder', '');
    $('#cancel-muscle-edit-button').addClass('d-none');
}

function addMuscleToExercise()
{
    $('#exercise-muscles').append($('#exercise-muscles').children('div').first().clone());
}

function editExercise(exerciseId, exerciseName, exerciseDescription, exerciseRelatedMuscleIds)
{
    exerciseName = decodeURIComponent(exerciseName);
    exerciseDescription = decodeURIComponent(exerciseDescription);
    exerciseRelatedMuscleIds = JSON.parse(decodeURIComponent(exerciseRelatedMuscleIds));

    $('form #exercise-id').val(exerciseId);
    $('form #exercise-name').val(exerciseName).attr('placeholder', exerciseName).focus();
    $('form #exercise-description').val(exerciseDescription).attr('placeholder', exerciseDescription).focus();
    for (var i = 0; i < exerciseRelatedMuscleIds.length; i++)
    {
        $('#exercise-muscles').children('div').eq(i).find('select').val(exerciseRelatedMuscleIds[i]);
        addMuscleToExercise();
        console.log(exerciseRelatedMuscleIds[i]);
    }
    $('#cancel-exercise-edit-button').removeClass('d-none');
}

function cancelExerciseEdit()
{
    $('form #exercise-id').val('');
    $('form #exercise-name').val('').attr('placeholder', '');
    $('form #exercise-description').val('').attr('placeholder', '');
    addMuscleToExercise();
    $('#exercise-muscles').children('div:not(:last)').remove();
    $('#cancel-exercise-edit-button').addClass('d-none');
}

function gatherExerciseMuscles()
{
    var muscleIds = [];
    $('#exercise-muscles select').each(function (index, select)
    {
        if ($(select).val() !== '')
            muscleIds.push(parseInt($(select).val()));
    });
    $('form #related-muscles').val(JSON.stringify(muscleIds));
}

function addExerciseToSet(addButtonClicked)
{
    $(addButtonClicked).parentsUntil('.card').last().children('.exercise-select').last().after(
        $(addButtonClicked).parentsUntil('.card').last().children('.exercise-select').last().clone()
    );
}

function addSetToWorkout()
{
    $('#workout-sets').children('div').last().after(
        $('#workout-sets').children('div').last().clone()
    );
    $('#workout-sets').children('div').last().find('input[type=checkbox]').attr('id', 'superset-'+supersetCounter);
    $('#workout-sets').children('div').last().find('label').attr('for', 'superset-'+supersetCounter);
    supersetCounter++;
}

function removeSetFromWorkout(removeButtonClicked)
{
    // do not remove the last set
    if ($('#workout-sets').children('div').length > 1)
        $(removeButtonClicked).parentsUntil('.card-columns').last().remove();
    else
        alert('Cannot remove last set.');
}

function gatherWorkoutSets()
{
    var sets = [];
    $('#workout-sets .card').each(function (i, card)
    {
        var set = {
            'exercise-id-numbers': [],
            'superset': $(card).find('input[type=checkbox]').is(':checked')
        };
        $(card).find('select').each(function (j, select)
        {
            if ($(select).val() != '')
            set['exercise-id-numbers'].push($(select).val());
        });
        sets.push(set);
    });
    $('#workout-form #sets').val(JSON.stringify(sets));
    return true;
}