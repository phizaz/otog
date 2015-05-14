#!/usr/bin/ruby

path_file = File.new("grader_path.txt")
path_file_content = path_file.read

solution_file = File.new(path_file_content)
answer_file = File.new("output.txt")
$result_file = File.new("grader_result.txt","w")

solution_file_content = solution_file.read
answer_file_content = answer_file.read

def correct
	$result_file.write("P")
	$result_file.close
	exit(0)
end
def wrong
	$result_file.write("W")
	$result_file.close
	exit(0)
end

solution_file_items = solution_file_content.split
answer_file_items = answer_file_content.split

if solution_file_items.length != solution_file_items.length
	wrong
else
	for i in 0..(solution_file_items.length-1)
		if solution_file_items[i] != answer_file_items[i]
			wrong
		end
	end
end
correct