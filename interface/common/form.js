function validateId(id, docIdError){
	let idError = document.getElementById(docIdError);
	id.value = id.value.replace(/[^\w-.]/g, '')
	idError.innerText = ids.has(id.value) ? 'Addon ID already in use' : '';
}

function pluginIdChanged(to, docInputPluginId){
	let inputPluginId = document.getElementById(docInputPluginId);
	inputPluginId.style.display = to === 'specified' ? '' : 'none';
}

function checkedAny(val, docIndividualGames){
	let individualGames = document.getElementById(docIndividualGames);
	individualGames.style.display = val ? 'none' : '';
}

function countChars(str, ch){
	let count = 0;
	for(let c of str) if(ch === c) ++count;
	return count;
}

function validateFiles(filesData, docDepsError){
	let depsError = document.getElementById(docDepsError);
	error = '';

	if(filesData.length > 0){
		files = filesData.split('\n');

		for(let file of files){
			let semis = countChars(file, ';');

			if(semis === 0){
				error = 'Error at file ' + file + ': path and filename must be separated by semicolon.';
				break;
			}else if(semis === 1){
				if(file.charAt(0) === ';'){
					error = 'Error at file ' + file + ': No path specified.';
					break;
				}else if(file.charAt(file.length - 1) === ';'){
					error = 'Error at file ' + file + ': No filename specified.';
					break;
				}
			}else{
				error = 'Error at file ' + file + ': multiple semicolons detected.';
				break;
			}
		}
	}

	filesError.innerText = error;
}

function depsChanged(val, docFilesError){
	let filesError = document.getElementById(docFilesError);
	let unknown = [];

	if(val.length > 0){
		let deps = val.trim().split(' ');

		deps.forEach(function(dep){
			if(!ids.has(dep)) unknown.push(dep);
		});
	}

	depsError.innerText = unknown.length > 0 ? ('Unknown addons: ' + unknown) : '';
}
