export interface ReviewTask {
  id: string
  target_type: string
  target_id: string
  task_type: string
  assigned_to_user_id: string | null
  due_at: string
  status: string
  result: string | null
  comment: string | null
  completed_at: string | null
}

export interface ReviewsIndexPageProps {
  can_manage: boolean
  assignee_options: { value: string, label: string }[]
  target_options: Record<string, { value: string, label: string }[]>
  tasks: ReviewTask[]
}
