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