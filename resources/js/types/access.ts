export interface AccessGrant {
  id: string
  project_id: string
  project_name: string
  environment_id: string | null
  environment_name: string | null
  resource_id: string
  resource_name: string
  user_id: string
  user_name: string
  grant_kind: string
  access_level: string
  scope_description: string | null
  owner_user_id: string
  owner_user_name: string
  granted_by_user_id: string | null
  granted_by_user_name: string | null
  status: string
  starts_at: string | null
  expires_at: string | null
  review_due_at: string | null
  revoked_at: string | null
  notes: string | null
}

export interface AccessIndexPageProps {
  can_manage: boolean
  project_options: { value: string, label: string }[]
  environment_options: { value: string, label: string, project_id: string }[]
  resource_options: { value: string, label: string }[]
  user_options: { value: string, label: string }[]
  access_grants: AccessGrant[]
}
