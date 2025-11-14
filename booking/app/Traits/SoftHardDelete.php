<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait SoftHardDelete
{
    use SoftDeletes;

    /**
     * Soft delete a record with confirmation
     */
    public function softDeleteWithConfirmation(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:' . $this->getTable() . ',id',
            'force_delete' => 'boolean'
        ]);

        $record = $this->findOrFail($request->id);
        $isForceDelete = $request->boolean('force_delete', false);

        try {
            if ($isForceDelete) {
                // Force delete (permanent removal)
                $record->forceDelete();
                $message = class_basename($this) . ' permanently deleted successfully.';
                Log::info('Force delete', [
                    'model' => class_basename($this),
                    'id' => $record->id,
                    'user_id' => auth()->id()
                ]);
            } else {
                // Soft delete
                $record->delete();
                $message = class_basename($this) . ' deleted successfully. It can be restored from the trash.';
                Log::info('Soft delete', [
                    'model' => class_basename($this),
                    'id' => $record->id,
                    'user_id' => auth()->id()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => $this->getRedirectAfterDelete()
            ]);

        } catch (\Exception $e) {
            Log::error('Delete failed', [
                'model' => class_basename($this),
                'id' => $request->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete ' . strtolower(class_basename($this)) . '. ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Restore a soft deleted record
     */
    public function restoreWithConfirmation(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:' . $this->getTable() . ',id'
        ]);

        try {
            $record = $this->withTrashed()->findOrFail($request->id);
            
            if (!$record->trashed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This ' . strtolower(class_basename($this)) . ' is not deleted.'
                ]);
            }

            $record->restore();
            
            Log::info('Restore', [
                'model' => class_basename($this),
                'id' => $record->id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => class_basename($this) . ' restored successfully.',
                'redirect' => $this->getRedirectAfterRestore()
            ]);

        } catch (\Exception $e) {
            Log::error('Restore failed', [
                'model' => class_basename($this),
                'id' => $request->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to restore ' . strtolower(class_basename($this)) . '. ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get deletion warnings for related records
     */
    public function getDeletionWarnings($record)
    {
        $warnings = [];
        
        // Check for related records that might be affected
        if (method_exists($this, 'bookings') && $this->bookings()->count() > 0) {
            $warnings[] = 'Has ' . $this->bookings()->count() . ' related bookings';
        }
        
        if (method_exists($this, 'sessionRecordings') && $this->sessionRecordings()->count() > 0) {
            $warnings[] = 'Has ' . $this->sessionRecordings()->count() . ' session recordings';
        }
        
        if (method_exists($this, 'feedback') && $this->feedback()->count() > 0) {
            $warnings[] = 'Has ' . $this->feedback()->count() . ' feedback records';
        }
        
        if (method_exists($this, 'payments') && $this->payments()->count() > 0) {
            $warnings[] = 'Has ' . $this->payments()->count() . ' payment records';
        }
        
        if (method_exists($this, 'notifications') && $this->notifications()->count() > 0) {
            $warnings[] = 'Has ' . $this->notifications()->count() . ' notifications';
        }

        return $warnings;
    }

    /**
     * Get redirect URL after delete
     */
    protected function getRedirectAfterDelete()
    {
        // Override in model if needed
        return null;
    }

    /**
     * Get redirect URL after restore
     */
    protected function getRedirectAfterRestore()
    {
        // Override in model if needed
        return null;
    }

    /**
     * Check if record can be deleted
     */
    public function canBeDeleted()
    {
        // Override in model for custom logic
        return true;
    }

    /**
     * Get reason why record cannot be deleted
     */
    public function getDeletionBlockReason()
    {
        // Override in model for custom logic
        return null;
    }
}
