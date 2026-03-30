export interface ProjectResource {
  id: string
  project_id: string
  project_name: string
  environment_id: string | null
  environment_name: string | null
  relation_type: string
  status: string
}

export interface Resource {
  id: string
  provider: string
  kind: string
  name: string
  external_identifier: string | null
  owner_user_id: string
  owner_user_name: string
  sensitivity: string
  status: string
  canonical_source: string | null
  notes: string | null
  archived_at: string | null
  project_links: ProjectResource[]
}

export interface ResourcesIndexPageProps {
  can_manage: boolean
  user_options: { value: string, label: string }[]
  resources: Resource[]
}

export interface ResourceShowPageProps {
  can_manage: boolean
  project_options: { value: string, label: string }[]
  environment_options: { value: string, label: string, project_id: string }[]
  resource: Resource
}
