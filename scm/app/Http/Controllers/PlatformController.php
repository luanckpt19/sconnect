<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Exception;

use App\Models\Platform;

class PlatformController extends Controller {

	/**
	* Show the application dashboard.
	*
	* @return \Illuminate\Contracts\Support\Renderable
	*/
	public function index() {
		$platform_list = Platform::orderBy('name', 'asc')->get();
		return view('platform')->with(compact('platform_list'));
	}
	
	public function savePlatform(Request $request) {
	    
	    $id = $request->input('id');	
	    $msg_success = 'Thêm mới thành công';
	    
		try {
			
			$messages = array(
				'name.required' => 'Chưa nhập tên nền tảng.',
				'name.unique' => 'Nền tảng này đã tồn tại',
				'website.required' => 'Chưa nhập website',
			);
		
			$validator = Validator::make($request->all(), [
			    'name' => 'required|unique:platforms,name,' . $id,
				'website' => 'required',
			], $messages);
			
			if ($validator->fails()) {
				$message = '';
				$messages = $validator->messages();
				foreach ($messages->all() as $msg) {
					$message .= $msg . '<br/>';
				}
				return redirect()->back()->withInput()->withErrors(['msg' => $message]);
			}
			
			/*
			 * Kiểm tra file. Nếu có file được upload lên thì store file vào thư mục /upload/images và lấy đường link của file để lưu vào database.
			 * */
			$picture_url = '';
			if ($request->hasFile('picture')) {
								
				$file = $request->picture;				
				$file_name = $file->getClientOriginalName();
				$lastpos_dot = strripos($file_name, '.');
				$file_ext = substr($file_name, $lastpos_dot);
				$file_name = substr($file_name, 0, $lastpos_dot);
				$file_name = Str::slug($file_name).$file_ext;
				
				$file->move(config('app.folder_upload') . '/images', $file_name);
				
				$picture_url = config('app.folder_upload') . '/images/' . $file_name;				
				
			}
						
			$platform_name = $request->input('name');
			$website = $request->input('website');
			
            if (is_numeric($id) && $id != 0) {
                // Kiểm tra xem trường hợp này có phải là update platform?
                $platform = Platform::find($id);
                $msg_success = 'Cập nhật thành công';
			} else {
			    //Trường hợp thêm mới platform.
			    $platform = new Platform();
			}			
			
            $platform->name = $platform_name;
            $platform->website = $website;
            if (!empty($picture_url)) {
                $platform->picture = $picture_url;
            }
            $platform->save();
			
            return redirect('/platform')->with('msg', $msg_success);
			
			
		} catch (QueryException $qe) {
			return redirect()->back()->withInput()->withErrors(['msg' => 'Lỗi: ' . $qe->getMessage()]);
		} catch (Exception $e) {
			return redirect()->back()->withInput()->withErrors(['msg' => 'Lỗi: ' . $e->getMessage()]);
		}
	}
	
	/*
	 * Xoá platform. Chỉ cho phép xoá platform không có kênh và video.
	 * */
	public function deletePlatform($id) {
	    try {
	        $platform = Platform::find($id);
	        $total_channels = $platform->total_channels;
	        $total_videos = 0;
	        if ($total_channels > 0 || $total_videos > 0) {
	            return response()->json(['status'=>'fail', 'message'=>'Platform đang có ' 
	                . $total_channels . ' kênh và ' . $total_videos . ' video. Không cho phép xoá.']);
	        } else {
	           $platform->delete();
	        }
	        return response()->json(['status'=>'success', 'message'=>'Xoá thành công']);
	    } catch (Exception $e) {
	        return response()->json(['status'=>'fail', 'message'=>'Lỗi: ' . $e->getMessage()]);
	    }
	}
}
